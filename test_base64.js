const b64 = "W3sidGVzdCI6Ilx1MzA0Mlx1MzA0NCJ9XQ=="; // [{"test":"\u3042\u3044"}] but base64'd from json_encode which keeps \uXXXX or actual unicode. 
// Actually, let's use actual unicode bytes in base64.
// PHP json_encode(["test" => "あ"]) -> '{"test":"\u3042"}'
