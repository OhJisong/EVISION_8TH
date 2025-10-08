# Reversing Basic Challenge Write-up

## 1. 문제 설명

사용자에게 문자열 입력을 받아 정해진 방법으로 입력값을 검증하여 `Correct` 또는 `Wrong`을 출력하는 프로그램입니다.

해당 바이너리를 분석하여 `Correct`를 출력하는 입력값을 찾아야 합니다.

문제의 핵심이 되는 `main` 함수 코드는 아래와 같이 나왔습니다.

```c
int __fastcall main(int argc, const char **argv, const char **envp)
{
  char v4[256]; // [rsp+20h] [rbp-118h] BYREF

  memset(v4, 0, sizeof(v4));
  sub_140001190("Input : ", argv, envp);         // "Input : " 출력
  sub_1400011F0("%256s", v4);                    // 사용자에게 문자열 입력 받음
  if ( (unsigned int)sub_140001000(v4) )         //  핵심: 입력값을 검증하는 함수
    puts("Correct");                             // 검증 성공
  else
    puts("Wrong");                               // 검증 실패
  return 0;
}
```

![main 사진]([https://github.com/YourUsername/YourRepo/blob/main/images/cat.jpg](https://github.com/OhJisong/EVISION_8TH/tree/main/week3/images/main.png)

## 2. 분석 과정

### `main` 함수 분석

1.  `v4`라는 256바이트 크기의 변수에 사용자 입력을 받습니다.
2.  `sub_140001000(v4)` 함수를 호출하여 입력값의 유효성을 검사합니다.
3.  `sub_140001000` 함수의 반환값이 **0이 아니면(참)** `"Correct"`를, **0이면(거짓)** `"Wrong"`을 출력합니다.

**목표**: `sub_140001000` 함수가 참(1)을 반환하게 만드는 입력값을 찾는 것입니다.

### `sub_140001000` 함수 분석 (핵심 로직) 

리버싱의 핵심은 바로 이 `sub_140001000` 함수의 동작을 파악하는 것입니다.

리버싱 도구(IDA)로 함수 내부를 분석하면 이 함수가 복잡한 암호화 로직이 아닌 **단순 문자열 비교** 로직을 수행한다는 것을 알 수 있습니다.

-   **동작 방식**: 함수는 사용자가 입력한 문자열과 프로그램 내부에 숨겨진(하드코딩된) 특정 문자열을 **직접 비교**합니다.
-   **핵심**: 이 **숨겨진 문자열**을 찾아내기만 하면 문제를 해결할 수 있습니다.

---

## 3. 해결 방법 🛠️

리버싱 도구를 사용하여 바이너리 파일의 데이터 섹션(data section)이나 `sub_140001000` 함수가 참조하는 문자열을 검색합니다.

검색 결과, 비교 대상으로 사용되는 문자열을 아래와 같이 찾을 수 있습니다.

`Compar3_the_str1ng`

---

## 4. 사진 첨부
![성공 사진]([https://github.com/YourUsername/YourRepo/blob/main/images/cat.jpg](https://github.com/OhJisong/EVISION_8TH/tree/main/week3/images/success.png)
