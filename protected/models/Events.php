<?php

/**
 * This is the model class for table "events".
 *
 * The followings are the available columns in table 'events':
 * @property integer $id
 * @property string $event
 * @property string $event_data
 * @property string $uri
 * @property string $created
 */
class Events extends CiiModel
{
	/**
	 * Adds the CTimestampBehavior to this class
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'CTimestampBehavior' => array(
				'class' 			=> 'zii.behaviors.CTimestampBehavior',
				'createAttribute' 	=> 'created',
				'updateAttribute' 	=> 'created',
				'timestampExpression' => time(),
				'setUpdateOnCreate' => false
			)
		);
	}

	/**
	 * Query Scoping
	 */
	public function scopes()
	{
		return array(
			'groupByUrl' => array(
				'group'   => 't.uri',
				'select'  => 't.uri, COUNT(*) as id',
				'order'   => 't.id ASC',
				'condition' => 't.event = "_trackPageView" AND t.created >= ' . strtotime("24 hours ago")
			),
		);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'events';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event, uri', 'required'),
			array('id', 'numerical', 'integerOnly'=>true),
			array('event, uri, ', 'length', 'max'=>255),
			array('event_data, created', 'safe'),
			array('id, content_id, page_title, event, event_data, uri,  created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event' => 'Event',
			'event_data' => 'Event Data',
			'uri' => 'URI',
			'content_id' => 'Content ID',
			'created' => 'Created',
		);
	}

	public function beforeValidate()
	{
		$this->event_data = CJSON::encode($this->event_data);
		return parent::beforeValidate();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('event',$this->event,true);
		$criteria->compare('event_data',$this->event_data,true);
		$criteria->compare('uri',$this->uri,true);
		$criteria->compare('content_id',$this->content_id,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Events the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
