<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
die();

/**
* ���� �� ���������� ������������ ����� �����
*/
$arResult["PATH_COMPONENT"] = $this->GetPath();

$this->IncludeComponentTemplate();
?>
