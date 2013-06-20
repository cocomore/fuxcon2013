---
title: Formatting
name: formatting
layout: topic
permalink: /formatting/
---
Our project requires two special kinds of text processing. Project lists only show abbreviated versions of project descriptions. Project detail pages show the full text, processed through Markdown. Markdown processing is not free; a production site will perform the translation from markup to HTML when projects are saved. Our Drupal natively implementation does it this way. The other three follow a more naive approach and integrate Markdown processing in to the presentation templates. 

## CakePHP
{% include cakephp/11-formatting.md %}

## Django
{% include django/11-formatting.md %}

## Drupal
{% include drupal/11-formatting.md %}

## Symfony
{% include symfony/11-formatting.md %}
