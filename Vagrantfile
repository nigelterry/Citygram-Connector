# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "geerlingguy/centos7"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 10000, host: 9000
  config.vm.network "forwarded_port", guest: 27017, host: 37017, auto_correct: true

  #config.vm.network "forwarded_port", guest: 9013, host: 9015

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"

  config.vm.synced_folder "./", "/var/www/html/citygram",
      owner: 48,
      group: 48,
      mount_options: ["dmode=777,fmode=777"]

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  # config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
  #   vb.gui = true
  #
  #   # Customize the amount of memory on the VM:
  #   vb.memory = "1024"
  # end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  # config.push.define "atlas" do |push|
  #   push.app = "YOUR_ATLAS_USERNAME/YOUR_APPLICATION_NAME"
  # end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
    config.vm.provision "shell", privileged: true, inline: <<-SHELL
    set -x

    sudo bash -c 'echo -e "root\nroot" | passwd root'

    sudo setenforce 0

    sudo yum -y install nano atop htop git

    sudo rpm -i https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
    sudo yum -y install php70w php70w-common php70w-opcache php70w-cli php70w-mysqlnd php70w-mbstring php70w-mcrypt php70w-intl php70w-pecl-xdebug php70w-devel
    sudo yum -y install gcc openssl-devel
    sudo /usr/bin/pecl install mongodb

    echo "extension=/usr/lib64/php/modules/mongodb.so" > /etc/php.d/mongodb.ini

    cat > /etc/yum.repos.d/mongodb-org-3.2.repo <<EOL
[mongodb-org-3.2]
name=MongoDB Repository
baseurl=https://repo.mongodb.org/yum/redhat/7/mongodb-org/3.2/x86_64/
gpgcheck=1
enabled=1
gpgkey=https://www.mongodb.org/static/pgp/server-3.2.asc
EOL

    sudo yum install -y mongodb-org
    sudo sed -i 's/bindIp/#bindIp/' /etc/mongod.conf
    sudo systemctl start mongod.service

    sudo yum -y install httpd
    sudo bash -c 'echo "IncludeOptional /vagrant/config/site.conf" > /etc/httpd/conf.d/vagrant.conf'
    sudo systemctl start httpd.service

    # Uncomment the following block to add a Webmin server for server management
    #
    wget -nv http://www.webmin.com/download/rpm/webmin-current.rpm
    sudo yum -y install perl-Net-SSLeay
    sudo rpm -i webmin-current.rpm
    sudo rm -f webmin-current.rpm

    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    cd /var/www/html/citygram
    /usr/local/bin/composer global require "fxp/composer-asset-plugin:~1.1.1"
    /usr/local/bin/composer install

    cat > /etc/php.d/xdebug.ini <<EOL
zend_extension=/usr/lib64/php/modules/xdebug.so
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_mode=req
xdebug.remote_connect_back=1
xdebug.remote_port=9013
EOL

crontab -l -u apache > mycron
echo MAILTO = NigelTerry@SapphireWebServices.com
echo "0 * * * * /var/www/html/citygram/yii.sh load PoliceReportRaleigh 30"  >> mycron
echo "5 * * * * /var/www/html/citygram/yii.sh load PoliceReportDurham 30"  >> mycron
echo "10 * * * * /var/www/html/citygram/yii.sh load PoliceReportCary 30"  >> mycron
echo "15 * * * * /var/www/html/citygram/yii.sh load PermitReportCary 30"  >> mycron
echo "20 * * * * /var/www/html/citygram/yii.sh load PermitReportRaleigh 30"  >> mycron
echo "25 * * * * /var/www/html/citygram/yii.sh load PermitReportDurham30"  >> mycron
echo "# 30 * * * * /var/www/html/citygram/yii.sh load ZoningReportRaleigh 30"  >> mycron
echo "35 * * * * /var/www/html/citygram/yii.sh load ZoningReportCary 30"  >> mycron
echo "40 * * * * /var/www/html/citygram/yii.sh load ZoningReportDurham 30"  >> mycron
echo "# 45 * * * * /var/www/html/citygram/yii.sh load CrashReportRaleigh 30"  >> mycron
echo "50 * * * * /var/www/html/citygram/yii.sh load CrashReportCary 30"  >> mycron
echo "# 55 * * * * /var/www/html/citygram/yii.sh load CrashReportDurham 30"  >> mycron
echo "0 * * * * /var/www/html/citygram/yii.sh load EventReportRaleigh 30"  >> mycron
crontab -u apache mycron
rm mycron



  SHELL

  config.vm.provision :shell, run: "always", privileged: false,  inline: <<-SHELL
    set -x
    sudo systemctl restart httpd.service mongod.service
  SHELL




end
