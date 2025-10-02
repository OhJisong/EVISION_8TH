<?php
// -----------------------------
// DB 연결 설정
// -----------------------------
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "my_db";
$db_port = 3307;

// MySQLi 연결
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

// 연결 오류 처리
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

// 멀티바이트 안전성을 위해 문자셋 지정
$conn->set_charset("utf8mb4");

// -----------------------------
// 입력값 수신
// -----------------------------
if (!isset($_POST['username'], $_POST['password'])) {
    // 비정상 접근 또는 누락된 요청 보호
    echo "<h1>요청 오류</h1>";
    echo "<p>아이디와 비밀번호를 입력하세요.</p>";
    echo '<a href="login.html">돌아가기</a>';
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

/* 
===============================================================================
[PATCH 설명 — SQL Injection 취약점 제거]
기존 취약 코드(문자열 연결):
    $sql = "SELECT * FROM users WHERE username = '$username' AND password='$password'";
    $result = $conn->query($sql);

문제: 사용자가 ' OR 1=1 -- 같은 값을 넣으면 쿼리 구조가 변조되어 로그인 우회가 가능

해결: "Prepared Statement" + "바인딩 변수" 사용
    1) 쿼리의 구조를 먼저 고정한다: WHERE username = ? AND password = ?
    2) 사용자의 입력값은 SQL의 "값"으로만 전달되어 문법으로 해석되지 않는다.
    → 따라서 주석(--), 따옴표('), OR 1=1 등의 페이로드가 들어가도 구조가 변하지 않아 우회 불가
===============================================================================
*/

// 1) 쿼리 구조를 플레이스홀더(?)로 고정
$stmt = $conn->prepare("SELECT id, username FROM users WHERE username = ? AND password = ? LIMIT 1");
if (!$stmt) {
    // prepare 실패 시 내부 오류를 그대로 노출하지 않도록 주의(여기서는 학습용)
    die("요청 처리 중 오류가 발생했습니다.");
}

// 2) 사용자 입력을 문자열(string, s)로 바인딩 (순서대로 username, password)
$stmt->bind_param("ss", $username, $password);

// 3) 실행
$stmt->execute();

// 4) 결과받기
$result = $stmt->get_result();

// -----------------------------
// 결과 처리
// -----------------------------
if ($result && $result->num_rows > 0) {
    // 출력 시 XSS 방지를 위해 escape
    $safe_name = htmlspecialchars($username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo "<h1>로그인 성공!</h1>";
    echo "<p>{$safe_name}님, 환영합니다.</p>";
} else {
    echo "<h1>로그인 실패</h1>";
    echo "<p>아이디 또는 비밀번호가 올바르지 않습니다.</p>";
    echo '<a href="login.html">다시 시도하기</a>';
}

// -----------------------------
// 자원 정리
// -----------------------------
$stmt->close();
$conn->close();
?>
