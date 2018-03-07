#!/bin/bash
clear

CODEPIER_URL=https://provision.codepier.io

NL='\033[0m'
RED='\033[0;31m'
GREEN='\033[38;05;151m'

echo "Hey there! We are starting to to setup your server for provisioning."

echo "Checking Privileges"
if [ "$(whoami)" != "root" ] ; then
   echo -e "$RED This script must be run as root. Please switch to the root user and run the command again. $NL"
   exit 1
fi
echo -e "$GREEN OK $NL"

echo "Checking System Compatibility"

server_os=`curl -s $CODEPIER_URL/os/$1`

os=$(cat /etc/*release | grep ^PRETTY_NAME | tr -d 'PRETTY_NAME="')
if ! [[ $os =~ $server_os ]]; then
  echo -e "$RED Required OS - Ubuntu 16.04 $NL"
  exit 1
fi
echo -e "$GREEN OK $NL"

ip=`wget -qO- https://provision.codepier.io/ip ; echo`
echo "We have detected your external IP as: $ip. Sending it off so that we know how to reach your server..."
curl -s --data "ip=$ip" $CODEPIER_URL/start/$1
echo -e "$GREEN OK $NL"

echo "Installing CodePier SSH key so that we can connect to your server..."

public_key=`curl -s $CODEPIER_URL/keys/$1/public`
private_key=`curl -s $CODEPIER_URL/keys/$1/private`

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
echo -e "$GREEN OK $NL"
echo "--"
curl -s -X GET $CODEPIER_URL/end/$1
echo -e "$GREEN We've completed the preliminary provisioning process! Please head back to CodePier.io, and watch the rest of the progress there! $NL"
