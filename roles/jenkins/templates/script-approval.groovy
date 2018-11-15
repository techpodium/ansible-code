#!groovy
import jenkins.model.Jenkins
import org.jenkinsci.plugins.scriptsecurity.scripts.*
import org.jenkinsci.plugins.scriptsecurity.scripts.languages.GroovyLanguage

/** Approving scripts which are embeded in the Jobs **/
def scriptApproval = Jenkins.instance.getExtensionList('org.jenkinsci.plugins.scriptsecurity.scripts.ScriptApproval')[0]
def hashesToApprove = scriptApproval.pendingScripts.collect{ it.getHash() }
hashesToApprove.each {
  scriptApproval.approveScript(it)
}


/** Approving Ansiblized scripts which are stored in Master Node **/
ScriptApproval sa = ScriptApproval.get();

def stored_scripts = []
def command = "ls -1 /var/lib/jenkins/tools/ | grep '.groovy'"
command.execute().text.eachLine { def script_name ->
	command = "cat /var/lib/jenkins/tools/${script_name}"
	def stored_script = command.execute().text
	if (!stored_script.equals("")){
    ScriptApproval.PendingScript s = new ScriptApproval.PendingScript(stored_script, GroovyLanguage.get(), ApprovalContext.create())
    sa.approveScript(s.getHash())
  }
}