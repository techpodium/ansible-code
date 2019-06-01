#!groovy

node('master'){

	def remote_host = "18.234.219.190"
	def git_repo_name = "github.com/rafioul/ansible-code.git"

	stage ('Buid Repository') {
		withCredentials([sshUserPrivateKey(credentialsId: "git-ssh-key", keyFileVariable: 'keyfile')]) {
			sh "scp -i ~/.ssh/grafana.pem ${keyfile} ubuntu@${remote_host}:/home/ubuntu/.ssh/id_rsa"

			sh (script: """ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} '
sudo rm -rf /opt/ghost; \
sudo mkdir -p /opt/ghost; \

cd /opt/ghost; \
sudo ssh-agent bash -c 'ssh-add /home/ubuntu/.ssh/id_rsa; git clone git@github.com:rafioul/ansible-code.git .'; \

sudo mkdir -p /opt/releases/ghost-\$(git log --format="%H" -n 1); \
sudo cp -R /opt/ghost/* /opt/releases/ghost-\$(git log --format="%H" -n 1); \
sudo chmod -R +x /opt/releases/ghost-\$(git log --format="%H" -n 1); \
if [ -L /opt/current-release ]; then \
	sudo ln -sfn \$(readlink -f /opt/current-release) /opt/previous-release; \
fi; \
sudo service nginx stop; \
sudo ln -sfn /opt/releases/ghost-\$(git log --format="%H" -n 1) /opt/current-release; \

if [ ! -L /var/www/ghost/start.sh ]; then \
	sudo ln -sf /opt/current-release /var/www/ghost; \
fi;

# yarn; \

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
""")
		}
	}
}
