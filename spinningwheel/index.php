<html lang="en"><head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="C6885ew6YZJpf0sGI8MZI2QnhPgjwDR69F4tDGpe">

    <link rel="dns-prefetch" href="//fonts.googleapis.com">

    <script type="text/javascript" async="" src="https://www.google-analytics.com/analytics.js"></script><script type="text/javascript" src="https://tools-unite.com/js/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700" type="text/css" media="all">
    <link href="https://tools-unite.com/css/styles.css" rel="stylesheet" type="text/css" media="all">
    <link href="https://tools-unite.com/css/icons.css" rel="stylesheet" type="text/css" media="all">

    <link rel="icon" href="https://tools-unite.com/img/favicon.png">

    <meta property="og:image" content="https://tools-unite.com/img/random-name-selector.png">
    <meta name="twitter:image" content="https://tools-unite.com/img/random-name-selector.png">

    <link rel="canonical" href="https://tools-unite.com/tools/random-picker-wheel">

    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->

</head>
<style>
    ::-webkit-scrollbar {
        display: none;
    }
    #content{
        background-color: transparent;
        height:100%;
    }

    #content-title{
        margin-bottom: 10px;
        color: white;
        margin-top: 20px;
        font-size: 50px;
        font-family: "Roboto Black";

    }

    #share-wheel-container{
        display: none;

    }

    #content-container{
        margin-top: 0;
        max-width: 100%;
        background-image: url("chicago_bg.png");
        background-position: center;
    }
    #header-logo{
        width: 200px;
        position: absolute;
        left: 0;
        top: 0;
        margin-left: 30px;
        margin-top: 30px;
    }
    #header-logo-url{
        float: left;
      margin-right: 18%;
    }
    #wheel-edit-container{
        margin-top: 20%;
    }

    #top_container{
        width: 100%;
        text-align: center;
    }
</style>
<body>

<div id="content-container">
    <div id="content">
        <div id="top_container">
            <a id="header-logo-url">
                <img src="bc_logo.png"  alt="Tools Unite Logo" id="header-logo">
            </a>
            <h1 id="content-title">WHEEL OF MISFORTUNE</h1>

        </div>

        <div id="content-body">
            <canvas id="wheel-canvas" height="462" width="528"></canvas>
        </div>
    </div>
</div>

<div id="wheel-edit-container">
    <h2 id="edit-wheel">Edit wheel</h2>
    <textarea id="wheel-textarea" autocomplete="off"></textarea>
    <button onclick="setup(true)">Update</button>
</div>

<div id="share-wheel-container">
    <input type="text" id="wheel-url" readonly="">
    <button id="share-button" onclick="copyWheelUrl()">Copy wheel</button>
</div>



<script src="https://tools-unite.com/js/wheel.js"></script>
<script>
    const textarea = document.getElementById("wheel-textarea");
    const wheel = document.getElementById("wheel-textarea");

    let isNewSetup = true;

    var canvas = document.querySelector('canvas');
    var ctx = canvas.getContext('2d');

    let radius;

    let useShortList = false;
    let useSmallFontSize = false;

    const windowWidth = $(window).width();
    if(windowWidth < 400) {
        useShortList = true;
        useSmallFontSize = true;
        radius = 110;
    }
    else if (windowWidth < 490) {
        useShortList = true;
        useSmallFontSize = true;
        radius = 300;
    }
    else if(windowWidth < 640) {
        radius = 300;
    }
    else {
        radius = 300;
    }

    canvas.height = radius*2.1;
    canvas.width = radius*2.4;

    const m = [canvas.width/2, canvas.height/2];
    let labels = ["FACEBOOK", "EMAIL SPAM", "EXAMPLE 3", "EXAMPLE 4", "EXAMPLE 5"];


    // const names = checkUrl();
    // if(names && names.length > 0) {
    //     labels = names;
    // }
    // else {
    //     if(useShortList) {
    //         labels = ["Facebook", "Email Spam", "another one"];
    //     }
    //     else {
    //         labels = ["Facebook", "Email Spam", "Dick in yo ear"];
    //     }
    // }

    const chosenWinnerFontSize = 50;
    const chosenRoundWinnerFontSize = useSmallFontSize ? 30 : 50;
    const setupMessageFontSize = useSmallFontSize ? 24 : 29;
    const removeWinnerLabelFontSize = useSmallFontSize ? 15 : 19;

    setupInputArea();
    let currentLabels = [];
    let labelPositions = [];
    let amount = 0;

    let nameIndex;

    let currentlyRotating = false;

    let radianInterval;

    const colors = ["#9f0e47", "#e70c4b", "#167CD9", "#1A2E40", "#00a69d"]

    setup(true);

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function drawIt(rotate) {
        clearCanvas();

        let start = 0;
        let end = radianInterval;
        let colorIndex = 0;

        for(let i=0; i<amount; i++) {
            ctx.save();
            ctx.beginPath();

            if(!rotate) {
                ctx.translate(m[0], m[1]);
            }

            ctx.moveTo(0, 0);
            ctx.arc(0, 0, radius, start, end);
            if(i>=amount-1) {
                ctx.lineTo(0, 0);
            }

            start += radianInterval;
            end += radianInterval;

            ctx.fillStyle = colors[colorIndex];
            ctx.fill();

            if(colorIndex >= colors.length-1) {
                colorIndex = 0;
            }
            else {
                colorIndex++;
            }

            ctx.strokeStyle = 'white';
            ctx.lineWidth = 8;
            ctx.stroke();

            ctx.closePath();
            ctx.restore();
        }

        drawPicker();
        drawMiddleCircle(rotate);

        drawLabels(rotate);

        if(amount === 1) {
            displayChosen();
        }
        else {
            canvas.addEventListener('click', rotateWheel);
        }

        if(isNewSetup) {
            displaySetupMessage();
        }
    }

    function displayChosen() {
        canvas.removeEventListener('click', rotateWheel);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.save();

        ctx.translate(0, (m[1]*0.70));
        ctx.rect(0, 0, canvas.width, m[1]*0.60);
        ctx.globalAlpha=0.8;
        ctx.fillStyle = "white";
        ctx.fill();

        ctx.fillStyle = 'white';
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.font = "bold " + chosenWinnerFontSize + "px Arial";
        ctx.fillText(currentLabels[0], m[0], m[1]*0.30);

        ctx.restore();
    }

    function displaySetupMessage() {
        ctx.save();


        ctx.restore();
        isNewSetup = false;
    }

    function drawMiddleCircle(rotate) {
        ctx.save();

        if(!rotate) {
            ctx.translate(m[0], m[1]);
        }

        ctx.arc(0, 0, radius/7, 0, 2*Math.PI);
        ctx.fill();

        ctx.restore();
    }

    function calculateLabelPositions() {
        let angle = radianInterval/2;

        for(let i=0; i<currentLabels.length; i++) {
            let opposite = Math.sin(angle);
            opposite = radius * opposite;
            opposite = parseInt(opposite);

            let adjacent = Math.pow(radius, 2)-Math.pow(opposite, 2);
            adjacent = Math.sqrt(adjacent);
            adjacent = parseInt(adjacent);

            if((angle<Math.PI/2) || (angle>=Math.PI*1.5)) {
                labelPositions.push([adjacent, opposite, angle]);
            }
            else if(angle>=Math.PI/2 && angle<Math.PI) {
                labelPositions.push([(adjacent*-1), opposite, angle]);
            }
            else if(angle>=Math.PI && angle<Math.PI*1.5) {
                labelPositions.push([(adjacent*-1), opposite, angle]);
            }
            else {
                labelPositions.push([adjacent, (opposite*-1), angle])
            }

            angle += radianInterval;
        }
    }

    function drawLabels(rotate) {
        ctx.fillStyle = 'white';
        ctx.textAlign = "right";
        ctx.textBaseline = "middle";
        ctx.font = "30Px arial";

        for(let i=0; i<currentLabels.length; i++) {
            ctx.save();

            if(!rotate) {
                ctx.translate(m[0]+labelPositions[i][0], m[1]+labelPositions[i][1]);
            }
            else {
                ctx.translate(labelPositions[i][0], labelPositions[i][1]);
            }

            ctx.rotate(labelPositions[i][2]);

            ctx.fillText(currentLabels[i]+" ", 0, 0);

            ctx.restore();
        }
    }

    function drawPicker() {
        ctx.save();
        ctx.beginPath();
        ctx.setTransform(1, 0, 0, 1, 0, 0);

        const arrowSize = 15;

        ctx.moveTo(m[0]+radius-10, m[1]);

        ctx.lineTo(m[0]+radius+13, m[1]+13);
        ctx.lineTo(m[0]+radius+27, m[1]+13);

        ctx.lineTo(m[0]+radius+27, m[1]+10);
        ctx.lineTo(m[0]+radius+27, m[1]-10);

        ctx.lineTo(m[0]+radius+27, m[1]-13);
        ctx.lineTo(m[0]+radius+13, m[1]-13);

        ctx.fillStyle = 'white';
        ctx.fill();

        ctx.closePath();
        ctx.restore();
    }

    function getWheelUrl() {
        let url = "https://tools-unite.com/tools/random-picker-wheel?names=";
        url += labels.join(',');

        const shareInput = document.getElementById('wheel-url');
        shareInput.value = url;
    }
</script>


</body></html>