# إعداد Firebase للمشروع الجديد

## 📋 ملخص سريع:

1. **في Firebase Console:**
   - أنشئ Realtime Database
   - أنشئ Storage
   - احصل على بيانات Web app من Project Settings
   - حمّل Service Account JSON

2. **في ملف `.env`:**
   - أضف متغيرات `VITE_FIREBASE_*` (للشات)
   - أضف متغيرات `FIREBASE_*` (للباك اند)

3. **في Laravel:**
   - ضع ملف Service Account في `storage/app/firebase/firebase-credentials.json`

4. **أعد تشغيل:**
   - `npm run dev` (للفرونت اند)
   - `php artisan config:clear` (للباك اند)

---

## 📝 الخطوات التفصيلية:

### 1. في Firebase Console (المشروع الجديد):

#### أ) إنشاء Realtime Database:
1. اذهب إلى **Build > Realtime Database**
2. اضغط **Create Database**
3. اختر **Location** (مثلاً: us-central1 أو asia-southeast1)
4. اختر **Start in test mode** (للاختبار) أو **Start in locked mode** (للتأمين لاحقاً)

#### ب) الحصول على بيانات الإعدادات:
1. اذهب إلى **Project Settings** (⚙️)
2. انزل إلى **Your apps** > اضغط **Web app** (أو أنشئ واحد جديد)
3. انسخ البيانات التالية:
   - **apiKey**
   - **authDomain**
   - **databaseURL** (من Realtime Database)
   - **projectId**
   - **storageBucket**
   - **messagingSenderId**
   - **appId**

#### ج) إعداد Realtime Database Rules (مؤقت للاختبار):
1. اذهب إلى **Realtime Database > Rules**
2. ضع القواعد التالية:
```json
{
  "rules": {
    ".read": true,
    ".write": true
  }
}
```
3. اضغط **Publish**

⚠️ **مهم**: بعد التأكد من عمل الشات، غيّر القواعد لقواعد آمنة!

#### د) إعداد Storage (لرفع الصور/الصوت):
1. اذهب إلى **Build > Storage**
2. اضغط **Get started**
3. اختر **Start in test mode**
4. في **Rules** ضع:
```
rules_version = '2';
service firebase.storage {
  match /b/{bucket}/o {
    match /{allPaths=**} {
      allow read, write;
    }
  }
}
```

#### هـ) الحصول على Server Key (للإشعارات):
1. اذهب إلى **Firebase Console > Project Settings**
2. افتح تبويب **Cloud Messaging**
3. في قسم **Cloud Messaging API (Legacy)** ستجد:
   - **Server key** (مفتاح طويل)
   - أو **Cloud Messaging API (V1)** - اضغط **Generate key pair** إذا لم يكن موجود
4. انسخ الـ **Server key** وضعها في `.env`:
   ```env
   FIREBASE_SERVER_KEY=AAAAXXXXXXXXXXXXXXXXXXXXXXXX
   ```

**ملاحظة:** إذا لم تجد Cloud Messaging:
- اذهب إلى **Build > Cloud Messaging**
- اضغط **Get started** لتفعيل الخدمة
- ثم ارجع إلى Project Settings > Cloud Messaging

#### و) تحميل Service Account JSON (للباك اند):
1. اذهب إلى **Project Settings > Service Accounts**
2. اضغط **Generate new private key**
3. حمّل الملف JSON
4. ضع الملف في: `storage/app/firebase/firebase-credentials.json`
5. تأكد من تحديث `FIREBASE_CREDENTIALS` في `.env`:
   ```env
   FIREBASE_CREDENTIALS=storage/app/firebase/firebase-credentials.json
   ```

---

### 2. في ملف `.env` (في جذر المشروع):

أضف المتغيرات التالية (استبدل القيم بقيم مشروعك الجديد):

#### أ) متغيرات Frontend (Vite - للشات):
```env
# Firebase Configuration (Frontend)
VITE_FIREBASE_API_KEY=your-api-key-here
VITE_FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
VITE_FIREBASE_DATABASE_URL=https://your-project-default-rtdb.firebaseio.com
VITE_FIREBASE_PROJECT_ID=your-project-id
VITE_FIREBASE_STORAGE_BUCKET=your-project.appspot.com
VITE_FIREBASE_MESSAGING_SENDER_ID=123456789
VITE_FIREBASE_APP_ID=1:123456789:web:xxxxx
```

#### ب) متغيرات Backend (Laravel - للإشعارات):
```env
# Firebase Configuration (Backend)
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_DATABASE_URL=https://your-project-default-rtdb.firebaseio.com
FIREBASE_STORAGE_BUCKET=your-project.appspot.com
FIREBASE_API_KEY=your-api-key-here
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_MESSAGING_SENDER_ID=123456789
FIREBASE_APP_ID=1:123456789:web:xxxxx
FIREBASE_SERVER_KEY=your-server-key-here
FIREBASE_CREDENTIALS=storage/app/firebase/firebase-credentials.json
```

**مثال كامل:**
```env
# Frontend (Vite)
VITE_FIREBASE_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
VITE_FIREBASE_AUTH_DOMAIN=brandy-new.firebaseapp.com
VITE_FIREBASE_DATABASE_URL=https://brandy-new-default-rtdb.firebaseio.com
VITE_FIREBASE_PROJECT_ID=brandy-new
VITE_FIREBASE_STORAGE_BUCKET=brandy-new.appspot.com
VITE_FIREBASE_MESSAGING_SENDER_ID=123456789012
VITE_FIREBASE_APP_ID=1:123456789012:web:abcdef123456

# Backend (Laravel)
FIREBASE_PROJECT_ID=brandy-new
FIREBASE_DATABASE_URL=https://brandy-new-default-rtdb.firebaseio.com
FIREBASE_STORAGE_BUCKET=brandy-new.appspot.com
FIREBASE_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
FIREBASE_AUTH_DOMAIN=brandy-new.firebaseapp.com
FIREBASE_MESSAGING_SENDER_ID=123456789012
FIREBASE_APP_ID=1:123456789012:web:abcdef123456
FIREBASE_SERVER_KEY=AAAAXXXXXXXXXXXXXXXXXXXXXXXX
FIREBASE_CREDENTIALS=storage/app/firebase/firebase-credentials.json
```

**ملاحظات:**
- `VITE_*` متغيرات للفرونت اند (تظهر في المتصفح)
- بدون `VITE_` متغيرات للباك اند فقط
- `FIREBASE_CREDENTIALS`: مسار ملف Service Account JSON
  - حمّله من: Firebase Console > Project Settings > Service Accounts > Generate new private key
  - ضعه في: `storage/app/firebase/firebase-credentials.json`

---

### 3. إعداد Service Account JSON (للباك اند):

1. **تحميل الملف:**
   - اذهب إلى Firebase Console > Project Settings > Service Accounts
   - اضغط **Generate new private key**
   - حمّل الملف JSON

2. **وضع الملف:**
   - أنشئ مجلد: `storage/app/firebase/` (إذا لم يكن موجود)
   - ضع الملف فيه باسم: `firebase-credentials.json`
   - تأكد من تحديث `.env`:
     ```env
     FIREBASE_CREDENTIALS=storage/app/firebase/firebase-credentials.json
     ```

---

### 4. إعادة تشغيل السيرفر:

بعد تحديث `.env`:
```bash
# للفرونت اند
npm run dev

# للباك اند (Laravel)
php artisan config:clear
php artisan cache:clear
```

---

### 5. التحقق من الإعدادات:

افتح Console في المتصفح وجرّب:
```js
console.log(import.meta.env.VITE_FIREBASE_DATABASE_URL);
```

يجب أن يطبع عنوان قاعدة البيانات الجديدة.

---

### 5. اختبار الشات:

1. تأكد من `localStorage.user` موجود:
```js
localStorage.setItem('user', JSON.stringify({
  id: "1",
  name: "Test User",
  avatar: "https://i.pravatar.cc/100?img=1"
}));
```

2. افتح زر الرسائل في الهيدر
3. استخدم زر "Start test chat" لإنشاء محادثة

---

---

## 🔧 الملفات التي تم تحديثها:

- ✅ `resources/js/useChat.js` - يستخدم `VITE_FIREBASE_*`
- ✅ `resources/js/components/Website/layout/Header.vue` - زر الشات
- ✅ `resources/js/components/Dashboard/layout/HeaderDashboard.vue` - زر الشات في الداشبورد
- ✅ `config/firbase.php` - إعدادات Firebase للباك اند
- ✅ `config/services.php` - إعدادات Firebase للباك اند

**لا حاجة لتعديل أي ملف كود** - فقط حدّث `.env` و Service Account JSON!

---

## ⚠️ ملاحظات مهمة:

- ⚠️ **القواعد المؤقتة**: القواعد المفتوحة للاختبار فقط! غيّرها لاحقاً لقواعد آمنة
- 🔒 **القواعد الآمنة لاحقاً**: بعد التأكد من عمل الشات، استخدم قواعد تسمح فقط للمستخدمين المسجلين
- 📱 **Storage**: تأكد من تفعيل Storage إذا كنت تريد رفع صور/صوت
- 🔄 **Cache**: إذا لم تظهر التغييرات، امسح كاش المتصفح أو استخدم Ctrl+Shift+R
- 🔑 **Service Account**: تأكد من وجود الملف في المسار الصحيح وأن Laravel يقرأه
- 🌐 **Database URL**: تأكد من أن العنوان يطابق مشروعك الجديد تماماً

