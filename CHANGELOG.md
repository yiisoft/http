# Yii HTTP Change Log

## 1.3.0 November 25, 2025

- Chg #58, #60: Change PHP constraint in `composer.json` to `7.4.* || 8.0 - 8.5` (@vjik)
- Enh #45: Improve `HeaderValueHelper` methods' annotations (@vjik)
- Enh #57: Simplify code of `HeaderValueHelper::getSortedValueAndParameters()` (@vjik)
- Enh #58: Add return value to closure for `preg_replace_callback()` in `HeaderValueHelper::getParameters()` (@vjik)
- Enh #59: Implement file name transliteration and remove `yiisoft/strings` dependency (@vjik)
- Bug #56: Add missed `ext-mbstring` dependency (@vjik)

## 1.2.0 November 09, 2021

- New #26: Add `HeaderValueHelper` that has static methods to parse header value parameters (@devanych)

## 1.1.1 February 10, 2021

- Chg: Update `yiisoft/strings` dependency (@samdark)

## 1.1.0 December 28, 2020

- Enh #12: Add `Method::ALL` and deprecated `Method::ANY` (@samdark)
- Enh #20: Add `ContentDispositionHeader` that generate `Content-Disposition` header name and value (@vjik)

## 1.0.0 September 1, 2020

- Initial release.
