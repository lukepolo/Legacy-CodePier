#!/bin/sh
clear
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root. Please switch to the root user and run the command again."
   exit 1
fi
echo "Hey there! Welcome to the CodePier automated custom server provisioning tool!"
echo "We're in the process of getting things setup, so that we can begin the provisioning process of your new custom server."
echo "This should only take a moment or two..."
echo "--"
ip=`wget -qO- https://provision.codepier.io/ip ; echo`
echo "We have detected your external IP as: $ip. Sending it off so that we know how to reach your server..."
curl -s --data "ip=$ip" provision.codepier.dev/start/$1
echo "--"
echo "Installing CodePier SSH key so that we can connect to your server..."
public_key=`curl -s provision.codepier.dev/keys/$1/public`
private_key=`curl -s provision.codepier.dev/keys/$1/private`
mkdir ~/.ssh -p
if [ ! -e ~/.ssh/id_rsa ]; then
    echo "$private_key" > ~/.ssh/id_rsa
else
    echo "$private_key" >> ~/.ssh/id_rsa
fi

if [ ! -e ~/.ssh/id_rsa.pub ]; then
    echo "$public_key" > ~/.ssh/id_rsa.pub
else
    echo "$public_key" >> ~/.ssh/id_rsa.pub
fi

if [ ! -e ~/.ssh/authorized_keys ]; then
    echo "$public_key" > ~/.ssh/authorized_keys
else
    echo "$public_key" >> ~/.ssh/authorized_keys
fi
echo "--"
curl -s -X GET provision.codepier.dev/end/$1
echo "We've completed the preliminary provisioning process! Please head back to CodePier.io, and watch the rest of the progress there! :)"
