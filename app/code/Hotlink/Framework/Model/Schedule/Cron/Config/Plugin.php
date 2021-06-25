<?php
namespace Hotlink\Framework\Model\Schedule\Cron\Config;
use Hotlink\Framework\Model\Schedule\Cron\Inject as I;
use Magento\Cron\Model\Config as Sb;
class Plugin {
	/**
	 * 2020-02-23 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "`n98-magerun2 sys:cron:run <...>`:
	 * «Argument 1 passed to N98\Magento\Command\System\Cron\AbstractCronCommand::getSchedule()
	 * must be of the type array, boolean given»": https://github.com/tradefurniturecompany/site/issues/127
	 * @param Sb $sb
	 * @param $r
	 * @return mixed
	 */
	function afterGetJobs(Sb $sb, $r) {
		$k = __METHOD__; /** @var string $k */
		if (!isset($sb->$k)) {
			$sb->$k = true;
			$i = df_o(I::class); /** @var I $i */
			$r = $i->execute($r);
		}
		return $r;
	}
}