# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "bento/centos-6.7"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  config.vm.network "forwarded_port", guest: 8080, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  config.vm.synced_folder ".", "/vagrant"

  config.vm.provision "shell", inline: <<-SHELL
    rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
    rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
    yum --enablerepo=remi,remi-php56 install -y php56 php56-php-mbstring mongodb-server git
    sudo ln -s `which php56` /usr/bin/php

    chkconfig mongod on
    /etc/init.d/mongod start

    cd /vagrant
    sudo -u vagrant php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    sudo -u vagrant php -r "if (hash_file('SHA384', 'composer-setup.php') === '070854512ef404f16bac87071a6db9fd9721da1684cd4589b1196c3faf71b9a2682e2311b36a5079825e155ac7ce150d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    sudo -u vagrant php composer-setup.php

    sudo -u vagrant php composer.phar global require 'fxp/composer-asset-plugin:~1.1.1'
    sudo -u vagrant php composer.phar install
  SHELL
end
