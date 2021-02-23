<?php

const RESPONSE_HTML = 'Html';
const RESPONSE_JSON = 'Json';
const RESPONSE_XML  = 'Xml';
const RESPONSE_JAVASCRIPT  = 'JavaScript';
const RESPONSE_IMAGE  = 'Image';
const RESPONSE_IMAGE_SVG  = 'ImageSvg';
const RESPONSE_IMAGE_PNG  = 'ImagePng';
const RESPONSE_IMAGE_JPG  = 'ImageJpg';
const RESPONSE_IMAGE_GIF  = 'ImageGif';
const RESPONSE_IMAGE_WEBP  = 'ImageWebp';
const RESPONSE_TEXT  = 'Text';

const MISSING_PRODUCTS_DEFAULT  = 'default';
const MISSING_PRODUCTS_MOVE_END  = 'move_end';
const MISSING_PRODUCTS_HIDE  = 'hide';

const MODULE_TYPE_PAYMENT  = 'payment';
const MODULE_TYPE_DELIVERY  = 'delivery';
const MODULE_TYPE_XML  = 'xml';

const TC_POSITION_HEAD = 'head';
const TC_POSITION_FOOTER = 'footer';

// Настройки canonical
const CANONICAL_ABSENT = 1;
const CANONICAL_PAGE_ALL = 2;
const CANONICAL_FIRST_PAGE = 3;
const CANONICAL_WITH_FILTER = 4;
const CANONICAL_CURRENT_PAGE = 5;
const CANONICAL_WITHOUT_FILTER = 6;
const CANONICAL_WITHOUT_FILTER_FIRST_PAGE = 7;

// Настройки <meta name="robots">
const ROBOTS_INDEX_FOLLOW = 1;
const ROBOTS_NOINDEX_FOLLOW = 2;
const ROBOTS_NOINDEX_NOFOLLOW = 3;