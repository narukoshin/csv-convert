<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COMPILER Control Panel</title>
</head>
<body>
    <style>
        * {
            padding: 0;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif
        }
        body {
            background-color: #0e0e0e;
        }
        #app {
            margin: 10rem auto;
            display: table;
            width: 23rem;
        }
        #app button {
            display: block;
            padding: 10px;
            border-radius: 200px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-bottom: 2rem;
            transition: all 500ms ease-in-out
        }
        #app button#start-compiler {
            box-shadow: 0 0 20px 0px #2196f3;
            border: 3px solid #2196f3;
            background-color: #2196f3
        }
        #app button#start-compiler:hover {
            border-color: #0f395b !important;
            background-color: #0f395b !important;
            box-shadow: 0 0 20px 0px #0f395b !important;

        }
        #app button#kill-compiler {
            box-shadow: 0 0 20px 0px #af332a;
            border: 3px solid #af332a;
            background-color: #af332a
        }
        #app button#kill-compiler:hover {
            border-color: #491612 !important;
            background-color: #491612 !important;
            box-shadow: 0 0 20px 0px #491612 !important;
        }
        .statuss {
            background-color: #1a1616;
            padding: 25px;
            margin-bottom: 2rem;
            border-radius: 5px;
            box-shadow: 0 0 20px 0px rgb(33 33 33 / 67%);
            user-select: none !important;
            text-align: center;
        }
        .statuss .statuss-message {
            display: inline-block;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }
        .statuss div {
            display: inline-block;
        }
        .statuss .not-running {
            background-color: #cc4e45;
            width: 13px;
            height: 13px;
            border-radius: 200px;
            vertical-align: middle;
            box-shadow: 0 0 20px 1px #ff5549;
            margin-left: 8px;
        }
        .statuss .running {
            background-color: #42c6b9;
            width: 13px;
            height: 13px;
            border-radius: 200px;
            vertical-align: middle;
            box-shadow: 0 0 20px 1px #42c6b9;
            margin-left: 8px;
        }
    </style>
    <div id="app">
        <!-- there will be the statuss of the compiler, it's running or not -->
        <div class="statuss">
            <div class="statuss-message">statuss:</div>
            <div v-if="Loading" class="loading-message">updating statuss...</div>
            <div v-else>
                <div v-if="ReadStatuss" class="running"></div>
                <div v-else class="not-running"></div>
            </div>
        </div>
        <!-- sone action buttons --> 
        <button v-on:click="startCompiler" id="start-compiler">Start a compiler</button>
        <button v-on:click="killCompiler" id="kill-compiler">Kill a process</button>
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