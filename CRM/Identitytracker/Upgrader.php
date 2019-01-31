<?php
use CRM_Identitytracker_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Identitytracker_Upgrader extends CRM_Identitytracker_Upgrader_Base {

  /**
   * Extension is enabled
   */
  public function enable() {
    // Add XCM matchers
    $customData = new CRM_Xcm_CustomData(E::LONG_NAME);
    $customData->syncOptionGroup(E::path('/resources/rules_option_group.json'));
  }

  /**
   * Extension is disabled
   */
  public function disable() {
    // Remove XCM matchers
    $matchers = ['CRM_Xcm_Matcher_IdTrackerInternalMatcher', 'CRM_Xcm_Matcher_IdTrackerExternalMatcher'];
    foreach ($matchers as $matcher_name) {
      $entry = civicrm_api3('OptionValue', 'get', [
          'name'            => $matcher_name,
          'option_group_id' => 'xcm_matching_rules']);
      if (!empty($entry['id'])) {
        civicrm_api3('OptionValue', 'delete', ['id' => $entry['id']]);
      }
    }
  }

  /**
   * Make sure the new XCM matchers are available
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_1301() {
    $this->ctx->log->info('Installing XCM matchers.');
    $this->enable();
    return TRUE;
  }

}
