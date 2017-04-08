# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.hostname = "new-age"
  config.vm.box = "ubuntu/trusty64"
  config.vm.synced_folder ".", "/home/xsolla"
      config.vm.network "private_network", ip: "10.1.1.33"

  config.vm.provider "virtualbox" do |vb|
     vb.memory = "1024"
  end

  config.vm.provision "shell", inline: <<-SHELL
     sudo apt-get update && sudo apt-get install python-software-properties
     sudo add-apt-repository ppa:ondrej/php
     sudo apt-get update && sudo apt-get -y upgrade
     sudo apt-get install -y php5.6-cli php5.6-mcrypt php5.6-mbstring php5.6-curl php5.6-mysql php5.6-gd php5.6-intl
     echo "mysql-server-5.6 mysql-server/root_password password root" | sudo debconf-set-selections
     echo "mysql-server-5.6 mysql-server/root_password_again password root" | sudo debconf-set-selections
     sudo apt-get install -y mysql-server-5.6
     mysql -uroot -proot -e "GRANT ALL ON *.* TO root; flush privileges;"
     sudo sed -i 's/bind-address/# bind-address/g' /etc/mysql/my.cnf
     sudo service mysql restart
  SHELL
end