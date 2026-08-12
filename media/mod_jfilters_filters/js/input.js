// noinspection DuplicatedCode

/**
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

'use strict';

// top namespace
window.JFilters = window.JFilters || {};

JFilters.input = {}

JFilters.input.update = (config, value) => {

    // Construct the url
    const moduleId = config.moduleId;
    const filterId = config.filterId;
    const baseUrl = config.baseUrl;
    const filterAlias = config.filterAlias;
    if (!baseUrl || !filterAlias) {
        return false;
    }
    const urlFormat = config.urlFormat ? config.urlFormat : 'query';

    // Get the url based on the selected values
    let url = JFilters.filteringModule.instances[moduleId].filters[filterId].createUrl(value, baseUrl, filterAlias, urlFormat);
    let pageUpdated = false;
    if (JFilters.filteringModule.instances[moduleId].filters[filterId]) {
        pageUpdated = JFilters.filteringModule.instances[moduleId].filters[filterId].update(url, !JFilters.filteringModule.instances[moduleId].submitWithButton);
    }

    if (!pageUpdated) {
        window.location.assign(url);
    }
    return true;
}

JFilters.input.getValue = (filterWrapper) => {
    let value = '';
    const input = filterWrapper.querySelector('.jfilters-filter-input__input');
    if (input) {
        value = input.value;
    }
    return value;
}

JFilters.input.setValues = (filterWrapper, value) => {
    const input = filterWrapper.querySelector('.jfilters-filter-input__input');
    if (input) {
        input.value = value;
    }
    return true;
}

JFilters.input.boot = () => {
    const filterParams = Joomla.getOptions('jfilters.filter'); // Return early
    if (typeof filterParams !== 'undefined') {
        filterParams.forEach(fltr => {
            // Improper config exit
            if (!fltr.extraProperties || fltr.extraProperties.type != 'input') {
                // Continue to the next one
                return;
            }
            let filterConfig = fltr.extraProperties;
            const moduleSelector = window.JFilters.filteringModule.moduleWrapperSelectorPrefix + fltr.moduleId;
            const moduleElement = document.querySelector(moduleSelector);
            if (!moduleElement) {
                return;
            }

            let config = {
                filterId: fltr.id,
                moduleId: fltr.moduleId,
                filterAlias: filterConfig.alias,
                baseUrl: filterConfig.baseUrl,
                urlFormat: filterConfig.urlFormat,
            };

            const filterWrapper = moduleElement.querySelector('#jfilters-filter-input-' + fltr.moduleId + '-' + fltr.id);
            const submitBtn = filterWrapper.querySelector('.jfilters-filter__submit-btn');
            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    let value = JFilters.input.getValue(filterWrapper);

                    const alertMessageElement = filterWrapper.parentElement.querySelector('.jfilters-filter__alert');
                    if (alertMessageElement) {
                        if (!value) {
                            alertMessageElement.innerText = Joomla.Text._('MOD_JFILTERS_FILTER_ALERT_MSG_ENTER_VALUE');
                        } else {
                            alertMessageElement.innerText = '';
                        }
                    }
                    JFilters.input.update(config, value);
                })
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", JFilters.input.boot);