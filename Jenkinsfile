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

cd /opt/ghost
sudo ssh-agent bash -c 'ssh-add /home/ubuntu/.ssh/id_rsa; git clone git@github.com:rafioul/ansible-code.git .'
EOF
"""
			String last_commit_hash = sh (script:"ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} 'cd /opt/ghost; git log --format=\"%H\" -n 1'", returnStdout:true 
			)
			String release_number = last_commit_hash.trim()

			sh (script: """ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} '
cd /opt/ghost;
sudo mkdir -p /opt/releases/ghost-\$(git log --format="%H" -n 1); \
sudo cp -R /opt/ghost/* /opt/releases/ghost-\$(git log --format="%H" -n 1); \
sudo chmod -R +x /opt/releases/ghost-\$(git log --format="%H" -n 1); \
if [ -L /opt/current-release ]; then \
	sudo ln -sfn \$(readlink -f /opt/current-release) /opt/previous-release; \
fi; \
sudo service nginx stop; \
sudo ln -sfn /opt/releases/ghost-\$(git log --format="%H" -n 1) /opt/current-release; \

if [ ! -L /var/www/ghost/start.sh ]; then \
	sudo ln -sfn /opt/current-release /var/www/ghost; \
fi;'
""")
		}
	}
}
