#!groovy


node('master'){

	def remote_host = "54.82.79.46"
	def git_repo_name = "github.com/rafioul/ansible-code.git"

	stage ('Buid Repository') {
		withCredentials([usernamePassword(credentialsId: 'git', passwordVariable: 'GIT_PASSWORD', usernameVariable: 'GIT_USERNAME')]){
			def gitUser = GIT_USERNAME
			def gitPass = URLEncoder.encode(GIT_PASSWORD, "UTF-8")

			wrap([$class: 'MaskPasswordsBuildWrapper', varPasswordPairs: [[password: "${gitPass}", var: 'masked_pass']]]) {
				def git_repo = "https://${gitUser}:${gitPass}@${git_repo_name}"

				sh """#!/bin/bash
ssh -i ~/.ssh/grafana.pem centos@${remote_host} << EOF
cd /opt/
sudo git clone ${git_repo}
cd ansible-code
chmod +x start.sh
./start.sh
EOF
"""
			}
		}
	}
}
