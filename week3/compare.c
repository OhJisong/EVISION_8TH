#include <stdio.h>

int main() {
    int userInput; // 사용자의 입력을 저장할 변수

    printf("Enter the secret number: ");

    // 사용자로부터 정수 하나를 입력받습니다.
    scanf("%d", &userInput);

    // 입력받은 값이 1234와 같은지 비교합니다.
    if (userInput == 1234) {
        puts("Correct"); // 같다면 Correct 출력
    } else {
        puts("Wrong");   // 다르다면 Wrong 출력
    }

    return 0;
}