<?php

session_start();

$data = array();

if (isset($_POST['submit'])) {

    if (!isset($_SESSION['google_code']) || !isset($_SESSION['access_token']))
//        header("Location: ../index.php");
        die('Нет токена');

    $data['id'] = $_POST['id'];
    $data['firstName'] = $_POST['firstName'];
    $data['lastName'] = $_POST['lastName'];
    $data['email'] = $_POST['email'];
    $data['phone'] = $_POST['phone'];

    $data['linkWithId'] = $_POST['linkWithId'];
    $data['etag'] = $_POST['etag'];

    $errors = false;

    if (!checkName($data['firstName']))
        $errors['firstName'] = 'Имя должно состоять не менее чем из двух символов';
    if (!checkName($data['lastName']))
        $errors['lastName'] = 'Фамилия должна состоять не менее чем из двух символов';
    if (!checkPhone($data['phone']))
        $errors['phone'] = 'Вы не верно ввели номер телефона';
    if (!checkEmail( $data['email']))
        $errors['email'] = 'Вы не верно ввели адрес электронной почты';

    if ($errors == false) {
        $contactXML = "
            <entry gd:etag=\"{$data['etag']}\">
              <id>{$data['id']}</id>
              <category scheme=\"http://schemas.google.com/g/2005#kind\"
                term=\"http://schemas.google.com/contact/2008#contact\"/>
              <gd:name>
                <gd:givenName>{$data['firstName']}</gd:givenName>
                <gd:familyName>{$data['lastName']}</gd:familyName>
                <gd:fullName>{$data['firstName']} {$data['lastName']}</gd:fullName>
              </gd:name>
              <content type=\"text\">Notes</content>
              <link rel=\"self\" type=\"application/atom+xml\" href=\"{$data['linkWithId']}\"/>
              <link rel=\"edit\" type=\"application/atom+xml\" href=\"{$data['linkWithId']}\"/>
              <gd:email rel=\"http://schemas.google.com/g/2005#other\" 
                address = \"{$data['email']}\" />
              <gd:phoneNumber rel=\"http://schemas.google.com/g/2005#other\" 
                primary=\"true\">{$data['phone']}</gd:phoneNumber>
              <gd:extendedProperty name=\"pet\" value=\"hamster\"/>
              <gContact:groupMembershipInfo deleted=\"false\"
                href=\"http://www.google.com/m8/feeds/groups/{$_SESSION['user_email']}/base/6\"/>
            </entry>
            </pre>";

        $headers = [
            'Host: www.google.com',
            'Authorization: Bearer ' . $_SESSION['access_token'],
            'X-HTTP-Method-Override: POST',
            'If-Match: ' . $data['etag'],
            'GData-Version: 3.0',
            'Content-length: ' . strlen($contactXML),
            'Content-Type: application/atom+xml; charset=UTF-8; type=feed'
        ];

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $data['linkWithId']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($contactXML));
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $result = curl_exec($ch);

        $info = curl_getinfo($ch);

        if (!curl_errno($ch)) {
            echo 'Прошло ', $info['total_time'], ' секунд во время запроса к ', $info['url'], "\n";

            echo '<pre>';
            print_r($info);
            echo '</pre>';
        } else {
            echo '<pre>';
            print_r($info);
            echo '</pre>';
        }

        curl_close($ch);

//        header("Location: ../index.php");
    }
}

function checkName($name) {
    if (strlen($name) >= 2)
        return true;
    return false;
}

function checkPhone($phone) {
    if (strlen($phone) >= 10)
        return true;
    return false;
}

function checkEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
        return true;
    return false;
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Google Contacts</title>
    <meta name="description" content="">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../css/main.min.css">
</head>

<body>

<section>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-lg-offset-4
                            col-sm-6 col-sm-offset-3
                            col-xs-10 col-xs-offset-1">

                <div class="contact-form">
                    <h2>
                        Редактирование
                        <img src="../img/logo_contact.png" alt="Google contacts">
                    </h2>

                    <hr />

                    <form action="updateContact.php" method="post">
                        <p><span class="color_element">*</span> Имя:</p>
                        <input type="text" name="firstName" class="form-control" placeholder=""
                               value="<?php if (isset($_POST['firstName'])) echo $_POST['firstName']; ?>"/>
                        <p id="text-error"><?php if (isset($errors['firstName'])) echo $errors['firstName']; ?></p>

                        <p><span class="color_element">*</span> Фамилия:</p>
                        <input type="text" name="lastName" class="form-control" placeholder=""
                               value="<?php if (isset($_POST['lastName'])) echo $_POST['lastName']; ?>"/>
                        <p id="text-error"><?php if (isset($errors['lastName'])) echo $errors['lastName']; ?></p>

                        <p><span class="color_element">*</span> Телефон:</p>
                        <input type="text" name="phone" class="form-control" placeholder=""
                               value="<?php if (isset($_POST['phone'])) echo $_POST['phone']; ?>"/>
                        <p id="text-error"><?php if (isset($errors['phone'])) echo $errors['phone']; ?></p>

                        <p><span class="color_element">*</span> E-mail:</p>
                        <input type="email" name="email" class="form-control" placeholder=""
                               value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>"/>
                        <p id="text-error"><?php if (isset($errors['email'])) echo $errors['email']; ?></p>

                        <input type="text" name="id" value='<?php echo $_POST['id']; ?>' style='display: none'/>

                        <input type='text' name='linkWithId' value='<?php if (isset($_POST['linkWithId'])) echo $_POST['linkWithId']; ?>' style='display: none'>
                        <input type='text' name='etag' value='<?php if (isset($_POST['etag'])) echo $_POST['etag']; ?>' style='display: none'>

                        <input type="submit" name="submit" value="Редактировать" class="btn btn-success" />
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>

</body>

</html>
