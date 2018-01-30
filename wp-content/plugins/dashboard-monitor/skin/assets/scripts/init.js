/* global dashboardMonitorLocalization */
import GenerateApiKey from './generateApiKey';

$(function() {

  const generateApiKey = new GenerateApiKey({
    addKeyAjaxAction: 'add_api_key_ajax',
    removeKeyAjaxAction: 'remove_api_key_ajax',
    nonceField: '#inf_dashboard_monitor_nonce',
    buttonSelector: '.js-dashboard-monitor-generate-key',
    inputSelector: '.js-dashboard-monitor-generate-key-name',
    removeSelector: '.js-dashboard-monitor-remove-key',
    keySelector: '.js-dashboard-monitor-key',
    listSelector: '.js-dashboard-monitor-list',
    listItemNewClass: 'dashboard-monitor-list__item--new',
    msgSelector: '.js-msg',
    msgStatusAttr: 'data-status',
  });

  // Add Key
  generateApiKey.$button.on('click', (event) => {
    event.preventDefault();
    generateApiKey.addKey();
  });

  generateApiKey.$inputField.on('keypress', (event) => {
    if (event.which === 13) {
      event.preventDefault();
      generateApiKey.addKey();
    }
  });

  // Remove Key
  generateApiKey.$list.on('click', generateApiKey.removeSelector, (event) => {
    event.preventDefault();

    generateApiKey.removeKey(event.target);
  });
  
});
