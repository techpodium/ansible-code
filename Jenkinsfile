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

				sh '''#!/bin/bash
ssh -i ~/.ssh/grafana.pem centos@${remote_host} << EOF
sudo mkdir -p /opt/ghost
sudo mkdir -p /opt/previous-release
sudo mkdir -p /opt/current-release

cd /opt/ghost
sudo rm -rf *
sudo rm -rf .[^.] .??*
sudo git clone ${git_repo} .
new_release=`cd /opt/ghost && git log --format="%H" -n 1`
echo $new_release
sudo mkdir /opt/releases/ghost-$new_release
EOF
'''
			}
		}
	}
}
