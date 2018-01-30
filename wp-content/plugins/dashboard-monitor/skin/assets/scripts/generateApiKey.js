/* global dashboardMonitorLocalization */

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
    this.msgStatusAttr = options.msgStatusAttr;
    this.listItemNewClass = options.listItemNewClass;
  }

  addKey() {

    const data = {
      action: this.addKeyAjaxAction,
      syncNonce: this.$nonceField.val(),
      name: this.$inputField.val(),
    };
    
    $.post(dashboardMonitorLocalization.dmAjaxUrl, data, (response) => {
      this.setMsg(response);

      if (response.status === 'error') {
        return false;
      }

      this.$inputField.val('');

      this.$list.find(this.key).remove();
      this.$list.children().removeClass(this.listItemNewClass);
      this.$list.prepend(this.getListItem(response.data));

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
      key: keyId,
    };

    $.post(dashboardMonitorLocalization.dmAjaxUrl, data, (response) => {
      this.setMsg(response);

      if (response.status === 'error') {
        return false;
      }

      $key.closest('li').remove();

      return false;
    }, 'json');

    return false;
  }

  getListItem(values) {
    return `
      <li class="dashboard-monitor-list__item ${this.listItemNewClass}">
        ${values.name}
        <a href="/wp-json/wp/v2/dashboard-monitor?api_key=${values.key}" target="_blank" class="dashboard-monitor-list__key js-dashboard-monitor-key">Open API Endpoint</a>
        <a href="#" class="dashboard-monitor-list__remove js-dashboard-monitor-remove-key" data-key-id="${values.id}"> Remove </a>
      </li>
    `;
  }

  setMsg(data) {
    if (typeof data === 'undefined') {
      return false;
    }

    this.$msg.html(`<div class="dashboard-monitor-msg__item">${data.msg}</div>`).attr(this.msgStatusAttr, data.status);
    return false;
  }
}
