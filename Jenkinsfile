#!groovy

node('master'){

	def remote_host = "18.234.219.190"
	def git_repo_name = "github.com/rafioul/ansible-code.git"

	stage ('Buid Repository') {
		withCredentials([sshUserPrivateKey(credentialsId: "git-ssh-key", keyFileVariable: 'keyfile')]) {
			sh "scp -i ~/.ssh/grafana.pem ${keyfile} ubuntu@${remote_host}:/home/ubuntu/.ssh/id_rsa"
			sh """#!/bin/bash -x
ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} << EOF
sudo rm -rf /opt/ghost
sudo mkdir -p /opt/ghost

# tempo
sudo rm -rf /opt/previous-release
sudo rm -rf /opt/current-release

cd /opt/ghost

sudo ssh-agent bash -c 'ssh-add /home/ubuntu/.ssh/id_rsa; git clone git@github.com:rafioul/ansible-code.git .'
EOF
"""
			last_commit = sh (script:"ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} 'cd /opt/ghost; git log --format=\"%H\" -n 1'", returnStdout:true )
			release_no = last_commit.trim()

			sh """#!/bin/bash -x
ssh -i ~/.ssh/grafana.pem ubuntu@${remote_host} << EOF

sudo mkdir -p /opt/releases/ghost-${release_no}
sudo cp -R /opt/ghost/* /opt/releases/ghost-${release_no}
sudo chmod -R +x /opt/releases/ghost-${release_no}

if [ -L /opt/current-release ]; then
	sudo ln -sfn \"\$(readlink -f /opt/current-release)\" /opt/previous-release
fi
# sudo service nginx stop
sudo ln -sfn /opt/releases/ghost-${release_no} /opt/current-release

EOF
"""
		}
	}
}
