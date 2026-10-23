-- Improve default ChatGPT prompts (only when shop still uses original emoji defaults).
-- Also fix EN brand keywords template that incorrectly used {$product}.

UPDATE `ok_settings_lang`
SET `value` = 'You are an e-commerce content editor. Write clear SEO-friendly copy using only facts from the name and provided specifications. Never invent technical details. Do not use emoji. Return only the requested text without preamble.'
WHERE `param` = 'ai_system_message'
  AND `lang_id` = 2
  AND `value` = 'You are a content manager. Only write metadata that will attract users'' attention and include keywords for search engine optimization. Use emoji. Paint everything in detail as much as possible';

UPDATE `ok_settings_lang`
SET `value` = 'Ты редактор контента для интернет-магазина. Пиши понятные SEO-тексты, используя только факты из названия и предоставленных характеристик. Не выдумывай технические детали. Не используй emoji. Возвращай только нужный текст без вступления.'
WHERE `param` = 'ai_system_message'
  AND `lang_id` = 1
  AND `value` = 'Ты контент менеджер. Пиши только метаданные, которые будут привлекать пользователей и включать ключевые слова для оптимизации поискового двигателя. Используй emoji. Расписывай по максимуму все в деталях';

UPDATE `ok_settings_lang`
SET `value` = 'Ти редактор контенту для інтернет-магазину. Пиши зрозумілі SEO-тексти, використовуючи лише факти з назви та наданих характеристик. Не вигадуй технічні деталі. Не використовуй emoji. Повертай лише потрібний текст без вступу.'
WHERE `param` = 'ai_system_message'
  AND `lang_id` = 3
  AND `value` = 'Ти контент менеджер. Пиши тільки метадані, які привертатимуть увагу користувачів і включатимуть ключові слова для оптимізації пошукового двигуна. Використовуй emoji. Розписуй по максимуму все в деталях';

UPDATE `ok_settings_lang`
SET `value` = 'Create a concise SEO meta title for the product {$product}. One line, no HTML, no trailing period.'
WHERE `param` = 'ai_product_title_template' AND `lang_id` = 2
  AND `value` = 'Create a meta title for product {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай краткий SEO meta title для товара {$product}. Одна строка, без HTML, без точки в конце.'
WHERE `param` = 'ai_product_title_template' AND `lang_id` = 1
  AND `value` = 'Создай мета-тайтл для товара {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи стислий SEO meta title для товару {$product}. Один рядок, без HTML, без крапки в кінці.'
WHERE `param` = 'ai_product_title_template' AND `lang_id` = 3
  AND `value` = 'Створи мета-тайтл для товару {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Create an SEO meta description for the product {$product}. 1-2 sentences, plain text, include a concrete fact when available.'
WHERE `param` = 'ai_product_meta_description_template' AND `lang_id` = 2
  AND `value` = 'Create a meta-description for the product {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай SEO meta description для товара {$product}. 1-2 предложения, обычный текст, по возможности укажи конкретный факт.'
WHERE `param` = 'ai_product_meta_description_template' AND `lang_id` = 1
  AND `value` = 'Создай мета-description для товара {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи SEO meta description для товару {$product}. 1-2 речення, звичайний текст, за можливості вкажи конкретний факт.'
WHERE `param` = 'ai_product_meta_description_template' AND `lang_id` = 3
  AND `value` = 'Створи мета-description для товару {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Write comma-separated SEO keywords for the product {$product}. No duplicates.'
WHERE `param` = 'ai_product_keywords_template' AND `lang_id` = 2
  AND `value` = 'Write keywords for the product {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Напиши SEO-ключевые слова для товара {$product} через запятую. Без дублей.'
WHERE `param` = 'ai_product_keywords_template' AND `lang_id` = 1
  AND `value` = 'Напиши ключевые слова для товара {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Напиши SEO-ключові слова для товару {$product} через кому. Без дублів.'
WHERE `param` = 'ai_product_keywords_template' AND `lang_id` = 3
  AND `value` = 'Напиши ключові слова для товару {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Create a short product summary for {$product}. 2-3 sentences.'
WHERE `param` = 'ai_product_annotation_template' AND `lang_id` = 2
  AND `value` = 'Create a short description for {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай краткое описание товара {$product}. 2-3 предложения.'
WHERE `param` = 'ai_product_annotation_template' AND `lang_id` = 1
  AND `value` = 'Создай краткое описание для товара {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи короткий опис товару {$product}. 2-3 речення.'
WHERE `param` = 'ai_product_annotation_template' AND `lang_id` = 3
  AND `value` = 'Створи короткий опис для товару {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Create a full HTML product description for {$product}. Use paragraphs and bullet lists. Use only provided facts.'
WHERE `param` = 'ai_product_description_template' AND `lang_id` = 2
  AND `value` = 'Create a full description for the product {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай полное HTML-описание товара {$product}. Используй абзацы и списки. Пиши только по предоставленным фактам.'
WHERE `param` = 'ai_product_description_template' AND `lang_id` = 1
  AND `value` = 'Создай полное описание для товара {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи повний HTML-опис товару {$product}. Використовуй абзаци та списки. Пиши лише за наданими фактами.'
WHERE `param` = 'ai_product_description_template' AND `lang_id` = 3
  AND `value` = 'Створи повний опис для товару {$product}';

UPDATE `ok_settings_lang`
SET `value` = 'Create a concise SEO meta title for the category {$category}. One line, no HTML, no trailing period.'
WHERE `param` = 'ai_category_title_template' AND `lang_id` = 2
  AND `value` = 'Create a meta title for category {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай краткий SEO meta title для категории {$category}. Одна строка, без HTML, без точки в конце.'
WHERE `param` = 'ai_category_title_template' AND `lang_id` = 1
  AND `value` = 'Создай мета-тайтл для категории {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи стислий SEO meta title для категорії {$category}. Один рядок, без HTML, без крапки в кінці.'
WHERE `param` = 'ai_category_title_template' AND `lang_id` = 3
  AND `value` = 'Створи мета-тайтл для категорії {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Create an SEO meta description for the category {$category}. 1-2 sentences, plain text.'
WHERE `param` = 'ai_category_meta_description_template' AND `lang_id` = 2
  AND `value` = 'Create a meta-description for the category {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай SEO meta description для категории {$category}. 1-2 предложения, обычный текст.'
WHERE `param` = 'ai_category_meta_description_template' AND `lang_id` = 1
  AND `value` = 'Создай мета-description для категории {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи SEO meta description для категорії {$category}. 1-2 речення, звичайний текст.'
WHERE `param` = 'ai_category_meta_description_template' AND `lang_id` = 3
  AND `value` = 'Створи мета-description для категорії {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Write comma-separated SEO keywords for the category {$category}. No duplicates.'
WHERE `param` = 'ai_category_keywords_template' AND `lang_id` = 2
  AND `value` = 'Write keywords for the category {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Напиши SEO-ключевые слова для категории {$category} через запятую. Без дублей.'
WHERE `param` = 'ai_category_keywords_template' AND `lang_id` = 1
  AND `value` = 'Напиши ключевые слова для категории {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Напиши SEO-ключові слова для категорії {$category} через кому. Без дублів.'
WHERE `param` = 'ai_category_keywords_template' AND `lang_id` = 3
  AND `value` = 'Напиши ключові слова для категорії {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Create a short category summary for {$category}. 2-3 sentences.'
WHERE `param` = 'ai_category_annotation_template' AND `lang_id` = 2
  AND `value` = 'Create a short description for the category {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай краткое описание категории {$category}. 2-3 предложения.'
WHERE `param` = 'ai_category_annotation_template' AND `lang_id` = 1
  AND `value` = 'Создай краткое описание для категории {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи короткий опис категорії {$category}. 2-3 речення.'
WHERE `param` = 'ai_category_annotation_template' AND `lang_id` = 3
  AND `value` = 'Створи короткий опис для категорії {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Create a full HTML category description for {$category}. Use paragraphs and bullet lists. Use only provided facts.'
WHERE `param` = 'ai_category_description_template' AND `lang_id` = 2
  AND `value` = 'Create a full description for the category {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай полное HTML-описание категории {$category}. Используй абзацы и списки. Пиши только по предоставленным фактам.'
WHERE `param` = 'ai_category_description_template' AND `lang_id` = 1
  AND `value` = 'Создай полное описание для категории {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи повний HTML-опис категорії {$category}. Використовуй абзаци та списки. Пиши лише за наданими фактами.'
WHERE `param` = 'ai_category_description_template' AND `lang_id` = 3
  AND `value` = 'Створи повний опис для категорії {$category}';

UPDATE `ok_settings_lang`
SET `value` = 'Create a concise SEO meta title for the brand {$brand}. One line, no HTML, no trailing period.'
WHERE `param` = 'ai_brand_title_template' AND `lang_id` = 2
  AND `value` = 'Create a meta title for brand {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай краткий SEO meta title для бренда {$brand}. Одна строка, без HTML, без точки в конце.'
WHERE `param` = 'ai_brand_title_template' AND `lang_id` = 1
  AND `value` = 'Создай мета-тайтл для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи стислий SEO meta title для бренда {$brand}. Один рядок, без HTML, без крапки в кінці.'
WHERE `param` = 'ai_brand_title_template' AND `lang_id` = 3
  AND `value` = 'Створи мета-тайтл для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Create an SEO meta description for the brand {$brand}. 1-2 sentences, plain text.'
WHERE `param` = 'ai_brand_meta_description_template' AND `lang_id` = 2
  AND `value` = 'Create a meta-description for the brand {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай SEO meta description для бренда {$brand}. 1-2 предложения, обычный текст.'
WHERE `param` = 'ai_brand_meta_description_template' AND `lang_id` = 1
  AND `value` = 'Создай мета-description для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи SEO meta description для бренда {$brand}. 1-2 речення, звичайний текст.'
WHERE `param` = 'ai_brand_meta_description_template' AND `lang_id` = 3
  AND `value` = 'Створи мета-description для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Write comma-separated SEO keywords for the brand {$brand}. No duplicates.'
WHERE `param` = 'ai_brand_keywords_template' AND `lang_id` = 2
  AND `value` IN (
    'Write keywords for the brand {$product}',
    'Write keywords for the brand {$brand}'
  );

UPDATE `ok_settings_lang`
SET `value` = 'Напиши SEO-ключевые слова для бренда {$brand} через запятую. Без дублей.'
WHERE `param` = 'ai_brand_keywords_template' AND `lang_id` = 1
  AND `value` = 'Напиши ключевые слова для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Напиши SEO-ключові слова для бренда {$brand} через кому. Без дублів.'
WHERE `param` = 'ai_brand_keywords_template' AND `lang_id` = 3
  AND `value` = 'Напиши ключові слова для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Create a short brand summary for {$brand}. 2-3 sentences.'
WHERE `param` = 'ai_brand_annotation_template' AND `lang_id` = 2
  AND `value` = 'Create a short description for the brand {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай краткое описание бренда {$brand}. 2-3 предложения.'
WHERE `param` = 'ai_brand_annotation_template' AND `lang_id` = 1
  AND `value` = 'Создай краткое описание для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи короткий опис бренда {$brand}. 2-3 речення.'
WHERE `param` = 'ai_brand_annotation_template' AND `lang_id` = 3
  AND `value` = 'Створи короткий опис для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Create a full HTML brand description for {$brand}. Use paragraphs and bullet lists. Use only provided facts.'
WHERE `param` = 'ai_brand_description_template' AND `lang_id` = 2
  AND `value` = 'Create a full description for the brand {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Создай полное HTML-описание бренда {$brand}. Используй абзацы и списки. Пиши только по предоставленным фактам.'
WHERE `param` = 'ai_brand_description_template' AND `lang_id` = 1
  AND `value` = 'Создай полное описание для бренда {$brand}';

UPDATE `ok_settings_lang`
SET `value` = 'Створи повний HTML-опис бренда {$brand}. Використовуй абзаци та списки. Пиши лише за наданими фактами.'
WHERE `param` = 'ai_brand_description_template' AND `lang_id` = 3
  AND `value` = 'Створи повний опис для бренда {$brand}';
