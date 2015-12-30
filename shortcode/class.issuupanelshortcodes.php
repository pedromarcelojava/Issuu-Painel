<?php

class IssuuPanelShortcodes implements IssuuPanelService
{
	private $config;

	private $shortcodeGenerator;

	public function __construct(IssuuPanelShortcodeGenerator $shortcodeGenerator)
	{
		$this->shortcodeGenerator = $shortcodeGenerator;
		add_shortcode('issuu-painel-document-list', array($this, 'deprecatedDocumentsList'));
		add_shortcode('issuu-painel-folder-list', array($this, 'deprecatedFolderList'));
		add_shortcode('issuu-panel-document-list', array($this, 'documentsList'));
		add_shortcode('issuu-panel-folder-list', array($this, 'folderList'));
		add_shortcode('issuu-panel-last-document', array($this, 'lastDocument'));
	}

	public function deprecatedDocumentsList($atts)
	{
		$content = '';
		$content .= $this->documentsList($atts);
		$content = "<em>" .
			get_issuu_message(
				"The [issuu-painel-document-list] shortcode is deprecated. Please, use [issuu-panel-document-list] using the same parameters."
			) .
			"</em>" . $content;
		return $content;
	}

	public function deprecatedFolderList($atts)
	{
		$content = '';
		$content .= $this->folderList($atts);
		$content = "<em>" .
			get_issuu_message(
				"The [issuu-painel-folder-list] shortcode is deprecated. Please, use [issuu-panel-folder-list] using the same parameters."
			) .
			"</em>" . $content;
		return $content;
	}

	public function documentsList($atts)
	{
		$content = '';
		$shortcodeData = $this->getShortcodeData('issuu-panel-document-list');
		$atts = shortcode_atts(array(
			'order_by' => 'publishDate',
			'result_order' => 'desc',
			'per_page' => '12',
		), $atts);
		$params = array(
			'pageSize' => $atts['per_page'],
			'startIndex' => ($atts['per_page'] * ($shortcodeData['page'] - 1)),
			'resultOrder' => $atts['result_order'],
			'documentSortBy' => $atts['order_by']
		);
		$content .= $this->shortcodeGenerator->getFromCache($shortcodeData['shortcode'], $atts, $shortcodeData['page']);

		if (empty($content))
		{
			try {
				$issuuDocument = $this->getConfig()->getIssuuServiceApi('IssuuDocument');
				$result = $issuuDocument->issuuList($params);
				$this->getConfig()->getIssuuPanelDebug()->appendMessage(
					"Shortcode [issuu-panel-document-list]: Request Data - " . json_encode($issuuDocument->getParams())
				);

				if ($result['stat'] == 'ok')
				{
					$docs = $this->getDocs($result);
					$content = $this->shortcodeGenerator->getFromRequest($shortcodeData, $atts, $result, $docs);
				}
				else
				{
					$this->getConfig()->getIssuuPanelDebug()->appendMessage(
						"Shortcode [issuu-panel-document-list]: " . $results['message']
					);
					$content = '<em><strong>Issuu Panel:</strong> E' . $results['code'] . ' '
						. get_issuu_message($documents['message']) . '</em>';
				}
			} catch (Exception $e) {
				$content = "<em><strong>Issuu Panel:</strong> ";
				$content .= get_issuu_message("An error occurred while we try list your publications");
				$content .= "</em>";
				$this->getConfig()->getIssuuPanelDebug()->appendMessage(
					"Shortcode [issuu-panel-document-list]: Exception - " . $e->getMessage()
				);
			}
		}
		return $content;
	}

	public function folderList($atts)
	{
		$content = '';
		$shortcodeData = $this->getShortcodeData('issuu-panel-folder-list');
		$atts = shortcode_atts(array(
			'id' => '',
			'order_by' => 'publishDate',
			'result_order' => 'desc',
			'per_page' => '12',
		), $atts);
		return $content;
	}

	public function lastDocument($atts)
	{
		$content = '';
		return $content;
	}

	public function setConfig(IssuuPanelConfig $config)
	{
		$this->config = $config;
	}

	public function getConfig()
	{
		return $this->config;
	}

	private function getShortcodeData($shortcode)
	{
		$post = get_post();
		$postID = (!is_null($post) && $this->getConfig()->getIssuuPanelCatcher()->inContent())? $post->ID : 0;
		$issuu_shortcode_index = $this->getConfig()->getNextIteratorByTemplate();
		$inHook = $this->getConfig()->getIssuuPanelCatcher()->getCurrentHookIs();
		$page_query_name = 'ip_shortcode' . $issuu_shortcode_index . '_page';
		$this->getConfig()->getIssuuPanelDebug()->appendMessage("Shortcode [$shortcode]: Init");
		$this->getConfig()->getIssuuPanelDebug()->appendMessage(
			"Shortcode [$shortcode]: Index " . $issuu_shortcode_index . ' in hook ' . $inHook
		);
		$shortcode = $shortcode . $issuu_shortcode_index . $inHook . $postID;
		return array(
			'shortcode' => $shortcode,
			'page_query_name' => $page_query_name,
			'in_hook' => $inHook,
			'issuu_shortcode_index' => $issuu_shortcode_index,
			'post' => $post,
			'page' => (isset($_GET[$page_query_name]) && is_numeric($_GET[$page_query_name]))?
				intval($_GET[$page_query_name]) : 1,
		);
	}

	private function getDocs($results)
	{
		$docs = array();
		foreach ($results['document'] as $doc) {
			$docs[] = array(
				'id' => $doc->documentId,
				'thumbnail' => 'http://image.issuu.com/' . $doc->documentId . '/jpg/page_1_thumb_large.jpg',
				'url' => 'http://issuu.com/' . $doc->username . '/docs/' . $doc->name,
				'title' => $doc->title,
				'date' => date_i18n('d/F/Y', strtotime($doc->publishDate)),
				'pubTime' => strtotime($doc->publishDate),
				'pageCount' => $doc->pageCount
			);
		}
		return $docs;
	}
}