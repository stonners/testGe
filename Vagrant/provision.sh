# Update
sudo apt-get update

# Avahi
sudo apt-get -y install avahi-daemon

# Apache
sudo sed -i s,/var/www/public,/var/www,g /etc/apache2/sites-available/000-default.conf
sudo service apache2 restart