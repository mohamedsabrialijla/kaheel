# إعداد نفاذ (Nafath) – من الوثيقة Nafath Authentication Service 3.0.1

## ربط الباك اند مع الـ Vue (تدفق Semati API – مثل الـ package)

| الخطوة | Backend (Laravel) | Vue (AddAd.vue) |
|--------|-------------------|------------------|
| **1. طلب تحقق** | `POST /user/auth/nafath/request` مع `{ id_number }` → `nafathRequest()` يستدعي `https://{subdomain}.semati.sa/nafath/api/v1/client/authorize/` ويرجع `trans_id` و `random` ويحفظ في الـ cache | المستخدم يدخل رقم الهوية في المودال، عند موافق يستدعي `auth/nafath/request` ثم يعرض الرقم `random` ويرسّل "افتح تطبيق نفاذ وأدخل الرقم" |
| **2. استدعاء من نفاذ (Callback)** | نفاذ يُرسل **POST** إلى `NAFATH_CALLBACK_URL` (مثلاً `https://www.brandy.sa/nafath/post-callback`) ومعامل `response` (JWT). `nafathPostCallback()` يفكّ JWT ويحدّث المستخدم ويرد `TRS` | — |
| **3. معرفة نتيجة التحقق** | الـ Vue تعمل **polling** على `GET /user/auth/nafath/check/{transId}` → ترجع حالة من الـ cache (`pending` / `completed` / `rejected`) بعد أن الـ callback يحدّثها | كل ~2.5 ثانية يستدعي `auth/nafath/check/:transId`؛ عند `completed` يغلق المودال ويعرض نجاح ويحدّث المستخدم |
| **4. حفظ/حالة التوثيق** | `POST /user/auth/nafath/save` و `GET /user/auth/nafath/status` كما هي للمستخدم الموثّق | يُستخدم عند الحاجة (الـ callback يحدّث المستخدم مباشرة فلا حاجة لحفظ إضافي لهذا التدفق) |

**متطلبات الـ .env:**

- `NAFATH_AUTHORIZE_URL` = الرابط الكامل لـ authorize (مثل `https://rabet-nafath.api.elm.sa/nafath/api/v1/client/authorize/`) — **لا يفتح في المتصفح**، يُستدعى من السيرفر فقط.
- `NAFATH_APP_ID` = (اختياري) إن كانت وثيقة ELM/نفاذ تطلب App ID أضفه هنا.
- `NAFATH_API_KEY` = المفتاح من نفاذ/ELM.
- `NAFATH_CALLBACK_URL` = الرابط الكامل لاستقبال الـ callback (مثل `https://www.brandy.sa/nafath/post-callback`).

---

## المشكلة: الرابط لا يفتح

إذا كان الرابط `https://nafath.sa/auth?callback=...` **لا يفتح** (خاصة في بيئة **Test**)، فغالباً تحتاج من الوثيقة التالي:

---

### 1) رابط بيئة الاختبار (Test / Sandbox)

في الوثيقة ابحث عن:

- **Test Environment** أو **بيئة الاختبار**
- **Base URL** أو **Sandbox URL**

مثال لو وجدت مثلاً:
`https://test.nafath.sa` أو `https://nafath-qa.sdaia.gov.sa`

ضعه في ملف **`.env`**:

```env
NAFATH_BASE_URL=https://test.nafath.sa
```

(استبدل بالرابط الفعلي من الدكيمنت)

---

### 2) مسار صفحة التحقق (Auth / Web View)

قد يكون المسار ليس `/auth` بل مثلاً:
`/verify` أو `/web/auth` أو غيره.

إذا وجدت في الوثيقة **مسار آخر** لصفحة التحقق، أضفه في `.env`:

```env
NAFATH_AUTH_PATH=/verify
```

(المسار الافتراضي الحالي: `/auth`)

---

### 3) اسم معامل الـ Callback

قد يسمون معامل الـ callback في الرابط باسم مختلف، مثلاً:
`redirect_uri` أو `return_url` بدل `callback`.

إذا ورد في الدكيمنت اسم مختلف، ضعه في `.env`:

```env
NAFATH_CALLBACK_PARAM=redirect_uri
```

(القيمة الافتراضي الحالية: `callback`)

---

### 4) ربما التدفق مختلف (استدعاء API أولاً)

بعض التكاملات تعتمد على:

1. استدعاء **API** أولاً (بعد تسجيل الدخول أو بمفتاح من نفاذ).
2. الـ API يرجع **رابط ويب أو رقم طلب**.
3. تُوجّه المستخدم لهذا الرابط (وليس لـ `https://nafath.sa/auth?callback=...` مباشرة).

في الوثيقة ابحث عن:

- **Initiate Request** أو **بدء طلب التحقق**
- **Web View URL** أو **User Redirect URL**
- **Test / Production** endpoints

واذكر لي (أو ضع هنا):

- الرابط الكامل لبدء التحقق في **Test**
- إن كان مطلوباً: **App ID / Client ID / API Key** واسم الـ header أو الـ body
- اسم المعامل الخاص برابط العودة (callback/redirect_uri/return_url)

---

### ملخص ما يُستخدم حالياً في المشروع

| الإعداد       | المتغير في `.env`     | الافتراضي          |
|---------------|------------------------|---------------------|
| قاعدة الرابط  | `NAFATH_BASE_URL`      | `https://nafath.sa` |
| مسار التحقق   | `NAFATH_AUTH_PATH`     | `/auth`             |
| معامل الـ callback | `NAFATH_CALLBACK_PARAM` | `callback`     |

بعد تعديل `.env` أعد تشغيل السيرفر وجرب مرة ثانية.  
إذا أرسلت المقتطفات من الدكيمنت (رابط Test + اسم المعامل + أي خطوات API)، أضبط الكود عليها بدقة.
