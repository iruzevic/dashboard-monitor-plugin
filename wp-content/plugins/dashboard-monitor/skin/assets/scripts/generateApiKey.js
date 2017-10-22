/* global dashboardMonitorLocalization */
import $ from 'jquery';

export default class GenerateApiKey {
  constructor(options) {
    this.addKeyAjaxAction = options.addKeyAjaxAction;
    this.removeKeyAjaxAction = options.removeKeyAjaxAction;
    this.removeSelector = options.removeSelector;
    this.key = options.keySelector;
    this.$button = $(options.buttonSelector);
    this.$inputField = $(options.inputSelector);
    this.$nonceField = $(options.nonceField);
    this.$list = $(options.listSelector);
    this.$removeLink = $(this.removeSelector);
    this.$msg = $(options.msgSelector);
  }

  init() {
    this.addKeyListener();
    this.removeKeyListener();
  }

  addKey() {

    const data = {
      action: this.addKeyAjaxAction,
      syncNonce: this.$nonceField.val(),
      name: this.$inputField.val()
    };
    
    $.post(dashboardMonitorLocalization.dmAjaxUrl, data, (response) => {

      this.$msg.html(response.msg);

      if (response.status === 'error') {
        return false;
      }

      this.$inputField.val('');

      this.$list.find(this.key).remove();
      this.$list.append(this.getListItem(response.data));

      return false;
    }, 'json');
  }

  removeKey(key) {

    if (typeof key === 'undefined') {
      return false;
    }

    const $key = $(key);
    const keyId = $key.attr('data-key-id');
    
    if (typeof keyId === 'undefined' || keyId === '') {
      return false;
    }

    const data = {
      action: this.removeKeyAjaxAction,
      syncNonce: this.$nonceField.val(),
      key: keyId
    };

    $.post(dashboardMonitorLocalization.dmAjaxUrl, data, (response) => {
      this.$msg.html(response.msg);

      if (response.status === 'error') {
        return false;
      }

      $key.parent().remove();

      return false;
    }, 'json');

    return false;
  }

  getListItem(values) {
    return `
      <li class="dashboard-monitor-list__item">
        ${values.name} : ${values.id} -- 
        <span class="dashboard-monitor-list__key js-dashboard-monitor-key">${values.key}</span>
        <a href="#" class="dashboard-monitor-list__remove js-dashboard-monitor-remove-key" data-key-id="${values.id}"> Remove </a>
      </li>
    `;
  }

  removeKeyListener() {
    this.$list.on('click', this.removeSelector, (event) => {
      event.preventDefault();
  
      this.removeKey(event.target);
    });
  }

  addKeyListener() {
    this.$button.on('click', (event) => {
      event.preventDefault();
      this.addKey();
    });

    this.$inputField.on('keypress', (event) => {
      if (event.which === 13) {
        event.preventDefault();
        this.addKey();
      }
    });
  }
}

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
    msgSelector: '.js-msg'
  });

  generateApiKey.init();
  
});
