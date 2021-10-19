UPDATE ok_pages AS p
    LEFT JOIN ok_lang_pages AS lp ON lp.page_id = p.id
    SET p.description       = '',
        p.meta_description  = '',
        lp.description      = '',
        lp.meta_description = ''
WHERE url = 'user/register';