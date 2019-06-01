#!groovy

node('master'){

	def remote_host = "18.234.219.190"
	def git_repo_name = "github.com/rafioul/ansible-code.git"

	stage ('Buid Repository') {
		withCredentials([sshUserPrivateKey(credentialsId: "git-ssh-key", keyFileVariable: 'keyfile')]) {
			sh "scp -i ~/.ssh/grafana.pem ${keyfile} ubuntu@${remote_host}:/home/ubuntu/.ssh/id_rsa"
			sh "deploy.sh"
		}
	}
}
