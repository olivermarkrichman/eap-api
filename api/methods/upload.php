<?php
$bucket_name = "eap-images";
$bucket_region = "eu-west-2";
require("./core/password.php");

    if (isset($_FILES['profile_img'])) {
        $file_name_array = explode(".", $_FILES['profile_img']['name']);
        $file_extension = array_pop($file_name_array);
        $file_name = $_FILES['profile_img']['name'] = uniqid() . "." . $file_extension;
        $temp_file_location = $_FILES['profile_img']['tmp_name'];

        $width = getimagesize($temp_file_location)[0];
        $height = getimagesize($temp_file_location)[1];
        $type = exif_imagetype($temp_file_location);
        $accepted_image_types = [2,3];

        if (!in_array($type, $accepted_image_types)) {
            response(400, "Image needs to be a PNG, JPG or JPEG");
        }

        if ($width > 200 && $height > 200) {
            response(400, "Image needs to be 200 x 200 or less");
        }

        require("../vendor/autoload.php");

        $s3 = new Aws\S3\S3Client([
            'region'  => $bucket_region,
            'version' => 'latest',
            'credentials' => [
                'key'    => $access_key_id,
                'secret' => $secret_access_key,
            ]
        ]);

        $result = $s3->putObject([
            'Bucket' => $bucket_name,
            'Key'    => "profile_imgs/" . $file_name,
            'SourceFile' => $temp_file_location,
            'ContentType' => "image/" . $file_extension
        ]);

        echo $result['ObjectURL'];
    } else {
        response(400, "No image uploaded");
    }
