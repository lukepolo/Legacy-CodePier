const filesizeParser = require('filesize-parser')

export const getBytesFromString = (string) => {
    return filesizeParser(string, { base: 10 })
}