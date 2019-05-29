#!groovy

node('master'){

	def remote_host = "52.200.5.208"
	def git_repo_name = "github.com/rafioul/ansible-code.git"

	stage ('Buid Repository') {
		withCredentials([usernamePassword(credentialsId: 'git', passwordVariable: 'GIT_PASSWORD', usernameVariable: 'GIT_USERNAME')]){
			def gitUser = GIT_USERNAME
      		def gitPass = URLEncoder.encode(GIT_PASSWORD, "UTF-8")

      		wrap([$class: 'MaskPasswordsBuildWrapper', varPasswordPairs: [[password: "${gitPass}", var: 'masked_pass']]]) {
    			def git_repo = "https://${gitUser}:${gitPass}@${git_repo_name}"

				sh """#!/bin/bash
ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} << EOF
sudo mkdir -p /opt/ghost
sudo mkdir -p /opt/previous-release
sudo mkdir -p /opt/current-release

sudo rm -rf /opt/ghost/*
cd /opt/ghost
sudo git clone ${git_repo} .
new_release=`cd /opt/ghost && git log --format="%H" -n 1`
sudo mkdir /opt/releases/ghost-$new_release
sudo cp -R /opt/ghost/* /opt/releases/ghost-$new_release
sudo chmod -R +x /opt/releases/ghost-$new_release
sudo rm -rf /opt/previous-release/*
sudo ln -sfn "$(readlink -f /opt/current-release/*)" /opt/previous-release
sudo service nginx stop
sudo rm -rf /opt/current-release/*
sudo ln -sfn /opt/releases/ghost-$new_release/* /opt/current-release
sudo rm -rf /var/www/ghost/*
sudo ln -sfn /opt/current-release/* /var/www/ghost/
cd /var/www/ghost/
# yarn
sudo ln -sf /var/www/ghost/system/files/ghost.audiomack.com.conf /etc/nginx/conf.d/ghost.audiomack.com.conf
sudo ln -sf /etc/nginx/sites-available/ghost.audiomack.com.conf /etc/nginx/sites-enabled/ghost.audiomack.com.conf
sudo service nginx restart

if ./start.sh; then
	echo "Build successful and doing clean-up"
	sudo ls -dt /opt/releases/ghost-*/ | tail -n +6 | xargs rm -rf
else
	echo "Build unsuccessful and starting rollback process"
	sudo service nginx stop
	sudo rm -rf /opt/current-release/*
	sudo ln -sfn $(readlink -f /opt/previous-release/*) /opt/current-release
	sudo service nginx restart
fi
EOF
"""
			}
		}
	}
}
