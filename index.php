<?php
/* I am using an if/else statement to verify if the user entered a name. If the person entered a name, we set that name as $_SESSION['name']. Since we are using a cookie-based session to store the name, we must call session_start() before anything is outputted to the browser. 
NB: the htmlspecialchars() func converts special characters to HTML entities, to protect the name variable from falling victims to cross-site scripting (XSS) */
session_start();

if(isset($_GET['logout'])){    
     
    //Simple exit message
    $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>". $_SESSION['name'] ."</b> has left the chat session.</span><br></div>";
    file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);
     
    session_destroy();
    header("Location: index.php"); //Redirect the user
}

if(isset($_POST["enter"])){
    if($_POST["name"] != ""){
        $_SESSION["name"] = stripslashes(htmlspecialchars($_POST["name"]));
    }
    else{
        echo "<span class='error'>Kindly type in a name</span>";

    }
}

/*The loginForm() function we created is composed of a simple login form which asks the user for their name, & would be verified with the if statement above */
function loginForm(){
    echo "
    <div id='loginform'>
    <p>Please enter your name to continue!</p>
    <form action='index.php' method='post'>
    <label for='name'>Name </label>
    <input type='text' name='name' id='name' />
    <input type='submit' name='enter' id='enter' value='Enter' />
    </form>
    </div>
    ";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <title>Chat Room</title>
    <meta name="description" content="Chat Room Application" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Lumineux">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    
<?php
        /* We will be using if statement to show the login form to a user who hasnt logged in */
if(!isset($_SESSION['name'])){
    loginForm();
}
else{
?>
    <main id="wrapper">
    <p id="app_name">Chat Room</p>
    <div id="menu">
        <span class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></span>
        <span class="logout"><a id="exit" href="#">Exit Chat</a></span>
        <hr>
    </div>

    <div id="chatbox">
    <?php
        if(file_exists("log.html") && filesize("log.html") > 0){
            $contents = file_get_contents("log.html");
        }
        ?>    
        </div>

        <form name="message" action="">
            <input name="usermsg" type="text" id="usermsg" placeholder="Message" size="40" />
            <input name="submitmsg" type="submit" id="submitmsg" value="Send" />
        </form>    
    </main>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        // jQuery Document
        $(document).ready(function () {
            $("#submitmsg").click(function() {
                var clientmsg = $("usermsg").val();
                $.post("post.php", { text: clientmsg });
                $("usermsg").val("");
                return false;
            });

            function loadLog() {
                varoldscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height before the request
                $.ajax({
                    url: "log.html",
                    cache: false,
                    success: function (html) {
                        $("#chatbox").html(html); //Insert chat log into the #chatbox div
                        //Autoscroll
                        var newscrollHeight = $("#chatbox")[0].scrollheight - 20; //Scroll height before the request
                        if(newscrollHeight > oldscrollHeight){
                            $("#chatbox").animate({ scrollTop: newscrollHeight}, 'normal'); //Autoscroll to bottom of div

                        }

                    }
                });
            }

            setInterval (loadLog, 2500);

               //if the user wants to end session
            $("#exit").click(function () {
                var exit = confirm("Are you sure you want to end the session?");
                if (exit == true) {
                window.location = "index.php?logout=true";
                }
                });
            });
        
    </script>
</body>
</html>
<?php
}
?>


