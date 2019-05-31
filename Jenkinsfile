#!groovy

node('master'){

	def remote_host = "18.234.219.190"
	def git_repo_name = "github.com/rafioul/ansible-code.git"

	stage ('Buid Repository') {
		withCredentials([sshUserPrivateKey(credentialsId: "git-ssh-key", keyFileVariable: 'keyfile')]) {
			sh "scp -i ~/.ssh/grafana.pem ${keyfile} ubuntu@${remote_host}:/home/ubuntu/.ssh/id_rsa"
			sh """ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} << EOF
sudo rm -rf /opt/ghost
sudo mkdir -p /opt/ghost
sudo mkdir -p /opt/previous-release
sudo mkdir -p /opt/current-release

cd /opt/ghost

sudo ssh-agent bash -c 'ssh-add /home/ubuntu/.ssh/id_rsa; git clone git@github.com:rafioul/ansible-code.git .'

sudo mkdir -p /opt/releases/ghost-\"\$(cd /opt/ghost && git log --format="%H" -n 1)\"
sudo cp -R /opt/ghost/* /opt/releases/ghost-\"\$(cd /opt/ghost && git log --format="%H" -n 1)\"
sudo chmod -R +x /opt/releases/ghost-\"\$(cd /opt/ghost && git log --format="%H" -n 1)\"
sudo rm -rf /opt/previous-release/*

if [ -L /opt/current-release/start.sh ]; then
	sudo ln -sfn \"\$(readlink -f /opt/current-release)\" /opt/previous-release
fi
sudo service nginx stop
sudo rm -rf /opt/current-release/*
sudo ln -sfn /opt/releases/ghost-\"\$(cd /opt/ghost && git log --format="%H" -n 1)\"/* /opt/current-release

if [ ! -L /var/www/ghost/start.sh ]; then
	sudo rm -rf /var/www/ghost/*
	sudo ln -sfn /opt/current-release/* /var/www/ghost/
fi

cd /var/www/ghost/
# yarn
sudo ln -sf /var/www/ghost/system/files/ghost.audiomack.com.conf /etc/nginx/conf.d/ghost.audiomack.com.conf
sudo service nginx restart

if ./start.sh; then
	echo "Build successful and doing clean-up"
	sudo ls -dt /opt/releases/ghost-*/ | tail -n +6 | xargs rm -rf
else
	echo "Build unsuccessful and starting rollback process"
	sudo service nginx stop
	if [ -L /opt/previous-release/start.sh ]; then
		sudo rm -rf /opt/current-release/*
		sudo ln -sfn \"\$(readlink -f /opt/previous-release/*)\" /opt/current-release
	else
		echo "/opt/previous-release is empty, nothing to rollback"
	fi
	sudo service nginx restart
fi
EOF
"""
		}
	}
}
