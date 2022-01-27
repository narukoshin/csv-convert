<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COMPILER Control Panel</title>
</head>
<body>
    <div id="app">
        <!-- there will be the statuss of the compiler, it's running or not -->
        <div class="statuss">
            <div v-if="Loading">Loading... please wait.</div>
            <div v-else>
                <div v-if="ReadStatuss" class="running">running</div>
                <div v-else class="not-running">Not running</div>
            </div>
        </div>
        <!-- sone action buttons --> 
        <button v-on:click="startCompiler">Start a compiler</button>
        <button v-on:click="killCompiler">Kill a process</button>
    </div>
    <script src="vendor/node_modules/vue/dist/vue.min.js"></script>
    <script>
        var app = new Vue({
            el: "#app",
            data(){
                return {
                    ReadStatuss: false,
                    Loading: true
                }
            },
            methods:{
                updateStatuss(){
                    fetch("server.php", {
                        method: "post",
                        body: JSON.stringify({"action":"GetProcessStatuss"})
                    })
                    .then(response => response.text())
                    .then(response => {
                        res = JSON.parse(response)
                        if (res.statuss == "C_OFF") {
                            this.ReadStatuss = false
                        } else if (res.statuss == "C_ON") {
                            this.ReadStatuss = true
                        }
                    })
                    this.Loading = false
                },
                startCompiler(){
                    fetch("server.php", {
                        method: "post",
                        body: JSON.stringify({"action":"StartCompiler"})
                    })
                },
                killCompiler(){
                    if (confirm("Are you sure that you want to kill this process?")){
                        fetch("server.php", {
                            method: "post",
                            body: JSON.stringify({"action":"killCompiler"})
                        })
                    }
                },
            },
            created(){
                this.interval = setInterval(() => this.updateStatuss(), 5000)
            }
        })
    </script>
</body>
</html>