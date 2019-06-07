#!groovy

node('master'){
	try {
		def remote_host = "54.242.228.243"
		def git_repo_name = "git@github.com:rafioul/ansible-code.git"

		stage ('Buid Repository') {
			withCredentials([sshUserPrivateKey(credentialsId: "ssh-key-ansible_code", keyFileVariable: 'keyfile')]) {
				sh "scp -i ~/.ssh/grafana.pem ${keyfile} ubuntu@${remote_host}:/home/ubuntu/.ssh/id_rsa"
			}

			withCredentials([sshUserPrivateKey(credentialsId: "git-ssh-key-scripts", keyFileVariable: 'keyfile')]) {
				sh "scp -i ~/.ssh/grafana.pem ${keyfile} ubuntu@${remote_host}:/home/ubuntu/.ssh/id_rsa_sub"
			}

				def statusCode = sh (script: """ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} '
sudo rm -rf /opt/releases/*; \
sudo rm -rf /opt/current-release; \
sudo rm -rf /opt/previous-release; \
sudo rm -rf /var/www/ghost; \

sudo rm -rf /opt/ghost; \
sudo mkdir -p /opt/ghost; \

cd /opt/ghost; \
sudo ssh-agent bash -c "ssh-add -D; ssh-add /home/ubuntu/.ssh/id_rsa; git clone ${git_repo_name} ."; \
sudo ssh-agent bash -c "ssh-add -D; ssh-add /home/ubuntu/.ssh/id_rsa_sub; git submodule update --init"; \

sudo chown -R ubuntu:ubuntu /opt/ghost; \

release_number=\$(git log --format="%H" -n 1); \
sudo mkdir -p /opt/releases/ghost-\$release_number; \
sudo chown -R ubuntu:ubuntu /opt/releases; \

sudo cp -r /opt/ghost/. /opt/releases/ghost-\$release_number; \
# sudo cp -r /opt/ghost/* /opt/releases/ghost-\$release_number; \

sudo chmod -R +x /opt/releases/ghost-\$release_number; \

if [ -L /opt/current-release ]; then \
	sudo ln -sfn \$(readlink -f /opt/current-release) /opt/previous-release; \
	sudo chown -R ubuntu:ubuntu /opt/previous-release/; \
	sudo chown -R ubuntu:ubuntu /opt/previous-release; \

fi; \

sudo service nginx stop; \
sudo ln -sfn /opt/releases/ghost-\$release_number /opt/current-release; \

if [ ! -L /var/www/ghost ]; then \
	sudo ln -sf /opt/current-release /var/www/ghost; \
	sudo chown -R ubuntu:ubuntu /var/www/ghost; \
	sudo chown -R ubuntu:ubuntu /var/www/ghost/; \
	#sudo chown -R ubuntu:ubuntu /var/www/ghost/*; \
fi; \

sudo chown -R ubuntu:ubuntu /opt/current-release; \
sudo chown -R ubuntu:ubuntu /opt/current-release/; \
#sudo chown -R ubuntu:ubuntu /opt/current-release/*; \

sudo ln -sf /var/www/ghost/system/files/ghost.audiomack.com.conf /etc/nginx/sites-available/ghost.audiomack.com.conf; \
sudo ln -sf /etc/nginx/sites-available/ghost.audiomack.com.conf /etc/nginx/sites-enabled/ghost.audiomack.com.conf; \
sudo service nginx restart; \

cd /var/www/ghost; \

if ./start.sh; then \
	echo "Build successful and doing clean-up"; \
	sudo ls -dt /opt/releases/ghost-*/ | tail -n +6 | xargs sudo rm -rf; \
else \
	echo "Build unsuccessful and starting rollback process"; \
	sudo service nginx stop; \
	if [ -L /opt/previous-release ]; then \
		sudo ln -sfn \$(readlink -f /opt/previous-release) /opt/current-release; \
	else \
		echo "/opt/previous-release is empty, nothing to rollback"; \
	fi; \
	sudo service nginx restart; \
fi; \
# rm -rf ~/.ssh/id_rsa;'
""", returnStatus: true)
		
		if(statusCode == 0)
			currentBuild.result = 'SUCCESS'
		else if(statusCode == 1)
			currentBuild.result = 'FAILURE'
		}
	}
	catch (Exception e) {
		currentBuild.result = 'FAILURE'
		echo e.toString()
		throw e
	}
	finally {

		if (currentBuild.result == 'FAILURE') {
			echo "Build unsuccessfull"
			//slack_notification("FAILURE", '#00FF00')
		} else {
			echo "Build successfull"
			//slack_notification("SUCCESS", '#FF0000')
		}
	}
}
