<?php
$bucket_name = "eap-images";
$bucket_region = "eu-west-2";
require("./core/password.php");

    if (isset($_FILES['profile_img'])) {
        $file_name_array = explode(".", $_FILES['profile_img']['name']);
        $file_extension = array_pop($file_name_array);
        $file_name = $_FILES['profile_img']['name'] = uniqid() . "." . $file_extension;
        $temp_file_location = $_FILES['profile_img']['tmp_name'];
        $image_to_upload = $temp_file_location;

        $width = getimagesize($temp_file_location)[0];
        $height = getimagesize($temp_file_location)[1];
        $type = exif_imagetype($temp_file_location);
        $accepted_image_types = [2,3];

        if (!in_array($type, $accepted_image_types)) {
            response(400, "Image needs to be a PNG, JPG or JPEG");
        }

        if ($width > 500 && $height > 500) {
            $width = 500;
            $height = 500;
            list($width_orig, $height_orig) = getimagesize($temp_file_location);
            $ratio_orig = $width_orig/$height_orig;

            if ($width/$height > $ratio_orig) {
                $width = $height*$ratio_orig;
            } else {
                $height = $width/$ratio_orig;
            }

            $image_p = imagecreatetruecolor($width, $height);
            $image = imagecreatefromjpeg($temp_file_location);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagejpeg($image_p, $temp_file_location . '.' . $file_extension);
            imagedestroy($image_p);
            $image_to_upload = $temp_file_location . '.' . $file_extension;
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
            'SourceFile' => $image_to_upload,
            'ContentType' => "image/" . $file_extension
        ]);

        $image_url = "'".$result['ObjectURL']."'";
        if (!empty($image_url)) {
            connect($image_url, function ($image_url, $conn) {
                $user_id = $GLOBALS['original_post']['user_id'];
                $q = "SELECT `id` FROM `users` WHERE `id` = ".$user_id;
                $res = $conn->query($q);
                if ($res->num_rows > 0) {
                    $q = "UPDATE `users` SET `profile_img` = $image_url WHERE id = ".$user_id;
                    if ($conn->query($q)) {
                        $q = "SELECT " . implode(", ", $GLOBALS['get_fields']['users']) . " FROM `users` WHERE `id` = ".$user_id;
                        $res = $conn->query($q);
                        if ($res->num_rows > 0) {
                            response(200, "Updated Successfully", false, $res->fetch_assoc());
                        } else {
                            response(500, "Failed to retrieve changed " . $endpoint_message_name, $pdo->error);
                        }
                    } else {
                        response(500, "Failed to update " . $endpoint_message_name, $pdo->error);
                    }
                } else {
                    response(404, ucfirst($endpoint_message_name) . " not found");
                }
            });
        } else {
            response(503, "Failed to upload profile picture");
        }
    } else {
        response(400, "No image uploaded");
    }
