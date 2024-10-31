#!/bin/bash

echo "host $EMAIL_HOST" >> /etc/msmtprc
echo "from $EMAIL_FROM" >> /etc/msmtprc
echo "user $EMAIL_USER" >> /etc/msmtprc
echo "password $EMAIL_PASS" >> /etc/msmtprc