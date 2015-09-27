echo "Bash version ${BASH_VERSION}..."
sudo echo -n "" > /var/log/squid/access.log
sudo usermod -G adm www-data
sudo chown proxy:adm /var/log/squid/access.log
