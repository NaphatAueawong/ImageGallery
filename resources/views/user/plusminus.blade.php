<html>
<head>

    <script type="text/javascript" src="jquery-1.2.6.min.js"></script>

</head>

<body>

    <script type="text/javascript">

        var count = 0;

        $(document).ready(function(){
            $("#plus").click(function () {
                count++;
                $("#value").html("count "+count);
            });
            $("#minus").click(function () {
                count--;
                $("#value").html("count "+count);
            })
        });

    </script>



    <div id="plusMinus">
        <p id="value"></p>
        <button id="plus">plus</button>
        <button id="minus">minus</button>
    </div>

</body>
</html>