# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vm.box = "scotch/box-pro"
    config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.hostname = "testge.local"

    config.vm.provision "shell", path: "Vagrant/provision.sh"
    config.vm.provision "shell", path: "Vagrant/startup.sh", run: "always"

    # On Windows we must check if the plugin vagrant-winnfsd if installed. This plugin must be installed to be able to use NFS
    if Vagrant::Util::Platform.windows? then
        unless Vagrant.has_plugin?("vagrant-winnfsd")
        raise  Vagrant::Errors::VagrantError.new, "vagrant-winnfsd plugin is missing. Please install it using 'vagrant plugin install vagrant-winnfsd' and rerun 'vagrant up --provision'"
        end
    end 

    # NFS
    config.vm.synced_folder ".", "/var/www", :nfs => { :mount_options => ["dmode=777","fmode=666"] }

    # BunkerStation - If you are not in BunkerPalace, comment on the line
    config.vm.synced_folder "/Volumes/shared/#{config.vm.hostname}/app", "/var/www/storage/app", :mount_options => ["dmode=777", "fmode=666"]

    # Give VM 1/4 system memory 
    config.vm.provider "virtualbox" do |v|
        host = RbConfig::CONFIG['host_os']

        # Retrieves memory depending on the device (mac OS, linux, windows)
        if host =~ /darwin/
            mem = `sysctl -n hw.memsize`.to_i / 1024
        elsif host =~ /linux/
            mem = `grep 'MemTotal' /proc/meminfo | sed -e 's/MemTotal://' -e 's/ kB//'`.to_i 
        elsif host =~ /mswin|mingw|cygwin/
            mem = `wmic computersystem Get TotalPhysicalMemory`.split[1].to_i / 1024
        end

        mem = mem / 1024 / 4
        v.customize ["modifyvm", :id, "--memory", mem]
    end

end