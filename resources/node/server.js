var base_path = __dirname.replace('resources/node', '');

require('dotenv').config({
    path: base_path + '.env'
});

var env = process.env;

/* Illuminate\Auth\SessionGuard@getName */
var loginSHA1 = 'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d';

// Packages
var fs = require('fs'),
    ioredis = require('ioredis'),
    cookie = require('cookie'),
    crypto = require('crypto'),
    PHPUnserialize = require('php-serialize');

// Variables
var users = {},
    server = null,
    offlineTimeout = {},
    appKey = env.APP_KEY,

    port = env.NODE_SERVER_PORT,
    redisSocket = new ioredis(),
    redisBroadcast = new ioredis();

if(appKey.startsWith('base64:')) {
    appKey = new Buffer(appKey.substring(7), 'base64');
}

if (env.APP_DEBUG !== 'true') {
    // console.log = function () {};
}

if (env.NODE_HTTPS === 'true') {
    console.log('HTTPS enabled');
    server = require('https').createServer({
        key: fs.readFileSync(env.SSL_KEY),
        cert: fs.readFileSync(env.SSL_CERT)
    });
} else {
    server = require('http').createServer();
}

console.log('Server on Port : ' + port);
server.listen(port);
var io = require('socket.io')(server);

redisBroadcast.psubscribe('*', function (err, count) {});
redisBroadcast.on('pmessage', function (fromSubscription, channel, message) {
    message = JSON.parse(message);
    console.log(message.event + ' to ' + channel);
    io.to(channel).emit(message.event, message.data);
});

io.use(function (socket, next) {
    if (typeof socket.request.headers.cookie != 'undefined') {
        redisSocket.get(getLaravelSessionIDFromCookie(socket.request.headers.cookie), function (error, session) {
            if (error) {
                console.log('ERROR :' + error);
            } else {
                var userIdentifier = socket.userID = getUserIDFromSession(session);

                if(userIdentifier) {

                    var room = users[userIdentifier] = socket.request.headers.referer.replace(/\/$/, '');

                    socket.join(room);

                    clearTimeout(offlineTimeout[userIdentifier]);

                    console.log(userIdentifier + ' logged in : joined ' + users[userIdentifier]);

                    socket.on('disconnect', function () {
                        console.log(userIdentifier + ' disconnected');
                        offlineTimeout[userIdentifier] = setTimeout(
                            function () {
                                console.log(userIdentifier + ' user left');
                                delete users[userIdentifier];
                            }, 3000
                        );
                    });
                    next();
                } else {
                    console.log('User is not authorized');
                    next(new Error('Not Authorized'));
                }
            }
        });
    } else {
        console.log('User does not have valid cookie request');
        next(new Error('Not Authorized'));
    }
});

io.on('connection', function (socket) {
   
});

function getLaravelSessionIDFromCookie(SocketCookie) {
    if (cookie.parse(SocketCookie).hasOwnProperty(env.APP_SESSION_COOKIE_NAME)) {
        return env.APP_CACHE_PREFIX + ':' + decryptLaravelCookie(cookie.parse(SocketCookie)[env.APP_SESSION_COOKIE_NAME]);
    }
}

function decryptLaravelCookie(cookie) {
    if (cookie) {
        var parsedCookie = JSON.parse(new Buffer(cookie, 'base64'));

        var iv = new Buffer(parsedCookie.iv, 'base64');
        var value = new Buffer(parsedCookie.value, 'base64');

        var decipher = crypto.createDecipheriv('aes-256-cbc', appKey, iv);

        var resultSerialized = Buffer.concat([
            decipher.update(value),
            decipher.final()
        ]);

        return PHPUnserialize.unserialize(resultSerialized.toString('utf8'));
    }
}

function getUserIDFromSession(session) {
    if (session) {
        try {
            var decryptedSession = PHPUnserialize.unserialize(decryptLaravelCookie(PHPUnserialize.unserialize(session).toString('utf8')), {}, {strict: false});

            if (decryptedSession.hasOwnProperty(loginSHA1)) {
                return decryptedSession[loginSHA1];
            } else if((env.NODE_ONLY_LOGGED_IN_USERS ? env.NODE_ONLY_LOGGED_IN_USERS : 'false') === 'false') {
                if(decryptedSession.hasOwnProperty('_token')) {
                    return decryptedSession._token;
                } else {
                    return decryptedSession[0]
                }

            }
        } catch (e) {
            console.log('Session data has object, cannot deserialize', e);
        }
    }
}