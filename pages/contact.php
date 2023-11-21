<!DOCTYPE html>
<html lang="en">

<head>
    <title>Contact - GreeLiving</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <link href="/assets/css/header.css" rel="stylesheet" />
    <link href="/assets/css/footer.css" rel="stylesheet" />
</head>

<body>
    <?php require("./components/header_applicant.php"); ?>

    <main style="padding-top: 100px">
        <h1>Contact us</h2>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Id laudantium corporis nisi delectus earum
                voluptate sapiente, error dolorum veniam vel soluta iure officia voluptates officiis, accusamus
                similique aspernatur dolore explicabo consectetur aperiam doloribus rerum! Impedit voluptas tempora
                voluptate mollitia magnam vero, porro minus, commodi quisquam, fuga provident magni harum animi
                possimus. Fugit a eveniet soluta. Inventore dignissimos repellat temporibus sapiente iste! Nostrum dolor
                quos, quia asperiores voluptatum dolores, quibusdam repellat, deserunt deleniti quod illo doloremque
                ducimus esse sed dicta consequatur animi expedita accusamus praesentium? Alias assumenda at eos tempora
                accusantium cumque esse doloribus mollitia magnam. Tempore sapiente cupiditate culpa illum?</p>

            <form method="post" action="">
                <label>
                    Email: <input type="text" name="email" />
                </label>

                <label>
                    Phone: <input type="text" name="phone" />
                </label>

                <label>
                    Name: <input type="text" name="name" />
                </label>

                <label>
                    Message: <textarea name="message" placeholder="Send us a message"></textarea>
                </label>

                <input type="submit" value="Submit" />
            </form>
    </main>

    <?php require("./components/footer.php"); ?>
</body>

</html>