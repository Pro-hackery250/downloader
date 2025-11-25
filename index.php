<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doovid.net</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>
    <nav>
        <ul>
            <div class="logo"><a href="#">DooVid.net</a></div>
            <div class="list" id="list">
                <li><a href="#"><i class="fas fa-toolbox"></i> All Tools</a></li>
                <li class="lan"><a href="#">English <i class="fas fa-angle-down"></i></a><div class="down"><a href="">English</a><a href="">Kinyarwanda</a></div></li>
            </div>
            <div class="menu" id="menu">
                <i>M</i>
            </div>
        </ul>
    </nav>

    <div class="intros">
        <h3>Online Video Downloader</h3>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit..</p>
        <div class="search">
            <input type="text" class="inpu" placeholder="Enter your link here">
            <a href="#" class="searc">Search</a>
        </div>
        <div class="end">
            <div class="line"></div>
            <h2>Designed by  <span>Dreambytes Team</span></h2>
            <div class="line"></div>
        </div>
        <div class="result"></div>
    </div>
    <div class="mores">
        <h2>Our Tools</h2>
        <div class="tools">
            <div class="tool">
                <img src="images/333.webp" alt="">
                <h4>Youtube Downloader</h4>
            </div>
            <div class="tool">
                <a href="">
                <img src="images/222.jpeg" alt="">
                <h4>Instagram Downloader</h4>
                </a>
            </div>
            <div class="tool">
                <a href="">
                <img src="images/111.jpg" alt="">
                <h4>TikTok Downloader</h4>
                </a>
            </div>
            <div class="tool">
                <a href="">
                <img src="images/444.png" alt="">
                <h4>Facebook Downloader</h4>
                </a>
            </div>
        </div>
    </div>
    <div class="abouts">
        <h2>Why should yu choose Doovid.net?</h2>
        <div class="abous">
            <div class="abou">
                <div class="pic"><img src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNGJjOGl2Mm1ieG44MmJqMHZ4NWZ5NXhuY3l3Y2VsYzV0ZHR2aDViYiZlcD12MV9zdGlja2Vyc19zZWFyY2gmY3Q9cw/we4c0PKLshW0gwmlVO/giphy.gif" alt=""></div>
                <div class="text">
                    <h3>No installation Required</h3>
                    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Alias possimus quod sunt culpa non, tenetur praesentium. Quo cupiditate similique, fuga tempora ducimus possimus repellendus debitis in rerum non veritatis aspernatur.</p>
                </div>
            </div>
            <div class="abou">
                <div class="pic"><img src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExMHpobjFjNm1uMGc0NmZqd3BwNnF3MjF5bHhpajhpdGMyNDhheTh0ZCZlcD12MV9zdGlja2Vyc19zZWFyY2gmY3Q9cw/Ui2NfksPeYq0rpdgqB/giphy.gif" alt=""></div>
                <div class="text">
                    <h3>Fast Download Speeds</h3>
                    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Alias possimus quod sunt culpa non, tenetur praesentium. Quo cupiditate similique, fuga tempora ducimus possimus repellendus debitis in rerum non veritatis aspernatur.</p>
                </div>
            </div>
            <!-- <div class="abou">
                <div class="pic"><img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExc25rYzg1bGR3b2d6dWlkcGJmOHhwb3htaGpmazJvcW4xYWRydDYzeiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/25J6jTBNU1ISFnpxVg/giphy.gif" alt=""></div>
                <div class="text">
                    <h3>Easy-to use the interface</h3>
                    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Alias possimus quod sunt culpa non, tenetur praesentium. Quo cupiditate similique, fuga tempora ducimus possimus repellendus debitis in rerum non veritatis aspernatur.</p>
                </div>
            </div> -->
        </div>
    </div>

    <footer>
        <h2>DooVid.net</h2>
        <div class="foots">
            <div class="foot">
                <li><a href="">Contact us</a></li>
                <li><a href="">Terms of srevice</a></li>
                <li><a href="">Privacy policy</a></li>
            </div>
            <div class="foot">
                <li><a href="">youtube to Mp3</a></li>
                <li><a href="">Youtube downloader</a></li>
                <li><a href="">Facebook Downloader</a></li>
            </div>
            <div class="foot">
                <li><a href="">instagram Downloader</a></li>
                <li><a href="">Tiktok Downloader</a></li>
                <li><a href="">Document Downloader</a></li>
            </div>
        </div>
        <p>&copy; Copyright 2025</p>
    </footer>
    <script>
const menu = document.getElementById('menu');
const menucont = document.getElementById('list');

menu.addEventListener('click', () =>{
    menucont.style.opacity = 1;
    menucont.style.top = '90.5px';
});
    </script>
    <script src="index.js"></script>
</body>
</html>