import africastalking from "africastalking";

const mysql = require("mysql");

const conn = mysql.createConnection({
  servername: "localhost",
  user: "programmer",
  password: "user@2999",
  database: "users",
  port: "3302",
});

conn.connect(function (err) {
  if (err) throw err;
  conn.query("SELECT phone_number FROM user", function (err, results, fields) {
    if (err) throw err;
    const phone = results.map((result) => {
      return "+" +result.phone_number;
    });
    const phone_numbers = phone;
    console.log(phone_numbers);

    const client = africastalking({
      apiKey:
        "0667ea64781b9fc514a0f44ca00611cd136be59c7783ec2d7d05ac6cf1ba4df0", // use your sandbox app API key for development in the test environment
      username: "sandbox",
    });

    client.SMS.send({
      to: phone_numbers,
      message:
        "Dear extension workers, fertilizer is now available in the deports",
      from: "ULIMI-WATHU",
    })
      .then(() => console.log("Message sent successifully"))
      .catch((err) => console.log(err));
  });
});
