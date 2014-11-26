<?php

return array(

    'username' => 'שם משתמש',
    'password' => 'סיסמא',
    'password_confirmation' => 'אשר סיסמא',
    'e_mail' => 'דוא"ל',
    'username_e_mail' => 'שם משתמש או דוא"ל',

    'signup' => array(
        'title' => 'הרשמה',
        'desc' => 'הרשמה לחשבון חדש',
        'confirmation_required' => 'אישור נדרש',
        'submit' => 'צור חשבון חדש',
    ),

    'login' => array(
        'title' => 'כניסה',
        'desc' => 'הזן את הפרטים שלך',
        'forgot_password' => '(שכחתי את הסיסמה)',
        'remember' => 'זכור אותי',
        'submit' => 'כניסה',
    ),

    'forgot' => array(
        'title' => 'שכחתי את הסיסמה',
        'submit' => 'הבא',
    ),

    'alerts' => array(
        'account_created' => 'חשבונך נוצר בהצלחה',
        'instructions_sent' => 'אנא בדוק את הדוא"ל שלך לקבלת ההוראות על איך לאשר את החשבון',
        'too_many_attempts' => 'ניסיונות רבים מדי. נסה שוב בעוד מספר דקות',
        'wrong_credentials' => 'שם משתמש, דואר אלקטרוני או סיסמא לא נכון',
        'not_confirmed' => 'חשבונך לא יכול להיות מאושר. בדוק את תיבת הדוא"ל שלך לקישור האישור',
        'confirmation' => 'חשבונך יאושר! עכשיו אתה יכול להתחבר',
        'wrong_confirmation' => 'קוד אישור שגוי',
        'password_forgot' => 'מידע לגבי יפוס סיסמא נשלח לדואר האלקטרוני שלך',
        'wrong_password_forgot' => 'משתמש לא נמצא',
        'password_reset' => 'הסיסמה שלך השתנתה בהצלחה',
        'wrong_password_reset' => 'סיסמא לא חוקית. נסה שוב',
        'wrong_token' => 'אסימון איפוס הסיסמה אינו חוקי',
        'duplicated_credentials' => 'אישורי הניתנים כבר בשימוש. נסה עם אישורי שונים',
    ),

    'email' => array(
        'account_confirmation' => array(
            'subject' => 'אישור חשבון',
            'greetings' => 'שלום :name',
            'body' => 'אנא לגשת לקישור הבא כדי לאשר את החשבון שלך',
            'farewell' => 'בברכה',
        ),

        'password_reset' => array(
            'subject' => 'איפוס סיסמא',
            'greetings' => 'שלום :name',
            'body' => 'אנא לגשת לקישור הבא כדי לשנות את הסיסמה שלך',
            'farewell' => 'בברכה',
        ),
    ),

);
