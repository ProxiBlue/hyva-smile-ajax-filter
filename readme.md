# Hyva Smile Elasticsuite Ajax Layered Navigation

**This is not production ready code, and is just an experimentation on ideas** 

## Introduction

This is a conceptual idea / playing around to turn the normal page reload filters into an AJAX based system
None of the results were checked if valid, and code is not super clean - Use purely for own ideas / implementation (at this stage)

I am not responsible for any production environment breaking if you use this code without your own testing.
It looks like it works, but I cannot guarantee valid results, or performance issues

## Requirements

* Hyva Theme for Magento
* Smile ElasticSuite Search with Autocomplete enabled
* Hyva Smile Compatibility module

## Install

* composer config repositories.github.repo.repman.io composer https://github.repo.repman.io
* composer require proxi-blue/ajax-layer
* ./bin/magento setup:upgrade
* ./bin/magento setup:di:compile

## Configuration

* enable admin setting: Catalog->Remember Category Pagination

