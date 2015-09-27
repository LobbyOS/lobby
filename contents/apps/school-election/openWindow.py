#  "(echo "pass"; echo "pass") | passwd root" 
import os
import sys

print "Enter the system number or to close, enter 'exit'"
while True:
	s = raw_input("Enter System Number : ")
	if(s == "exit"):
		sys.exit(0)

	os.system("ssh root@192.168.10.1 'sudo echo -n '' > /var/log/squid/access.log && sudo usermod -G adm www-data && sudo chown proxy:adm /var/log/squid/access.log'")

	print "Opening in System", s ," - 192.168.10.", str(s)

	os.system('ssh root@192.168.10.'+ str(s) +' "export DISPLAY=:0;pkill firefox;firefox http://election.dev -fullscreen > /dev/null & exit;"')
