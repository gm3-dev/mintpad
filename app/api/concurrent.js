const { spawn } = require('child_process');
function runScript(scriptName) {
    return spawn('node', [scriptName], { stdio: 'inherit' });
}
const cronjob = runScript('cronjob.js');
const profileupload = runScript('profileupload.js');
const test = runScript('test.js');
cronjob.on('close', (code) => {
    console.log(`cronjob.js exited with code ${code}`);
});
// just testing redeployment from git commit
profileupload.on('close', (code) => {
    console.log(`profileupload.js exited with code ${code}`);
});

test.on('close', (code) => {
    console.log(`test.js exited with code ${code}`);
});
