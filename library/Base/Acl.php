<?php

class Base_Acl extends Zend_Acl {

    public function __construct() {
        $this->setRoles();
        $this->setResources();
        $this->setPrivilages();
    }

    public function setRoles() {
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('user'), 'guest');
        $this->addRole(new Zend_Acl_Role('assistant'), 'guest');
        $this->addRole(new Zend_Acl_Role('patient'), 'guest');
        $this->addRole(new Zend_Acl_Role('doctor'), 'guest');
//        $this->addRole(new Zend_Acl_Role('administrator'), 'user');
        $this->addRole(new Zend_Acl_Role('administrator'));
        $this->addRole(new Zend_Acl_Role('subadmin'), 'user');
    }

    public function setResources() {

        /** Admin module */

        
        $this->add(new Zend_Acl_Resource('admin'))
                ->add(new Zend_Acl_Resource('admin:index', 'admin'))
                ->add(new Zend_Acl_Resource('admin:email-template', 'admin'))
                ->add(new Zend_Acl_Resource('admin:email-campaign', 'admin'))
                ->add(new Zend_Acl_Resource('admin:global-settings', 'admin'))
                ->add(new Zend_Acl_Resource('admin:login', 'admin'))
                ->add(new Zend_Acl_Resource('admin:user', 'admin'))
                ->add(new Zend_Acl_Resource('admin:userlevel', 'admin'))
                ->add(new Zend_Acl_Resource('admin:article', 'admin'))
                ->add(new Zend_Acl_Resource('admin:insurance-company', 'admin'))
                ->add(new Zend_Acl_Resource('admin:association', 'admin'))
                ->add(new Zend_Acl_Resource('admin:reason-for-visit', 'admin'))
                ->add(new Zend_Acl_Resource('admin:doctor', 'admin'))
                ->add(new Zend_Acl_Resource('admin:category', 'admin'))
                ->add(new Zend_Acl_Resource('admin:patient-comment', 'admin'))
                ->add(new Zend_Acl_Resource('admin:timeslot', 'admin'))
                ->add(new Zend_Acl_Resource('admin:award', 'admin'))
                ->add(new Zend_Acl_Resource('admin:hospital-affiliation', 'admin'))
                ->add(new Zend_Acl_Resource('admin:appointment', 'admin'))
                ->add(new Zend_Acl_Resource('admin:patient', 'admin'))
                ->add(new Zend_Acl_Resource('admin:phoneappointment', 'admin'))
                ->add(new Zend_Acl_Resource('admin:docregistration', 'admin'))
                ->add(new Zend_Acl_Resource('admin:comingsoon', 'admin'))
                ->add(new Zend_Acl_Resource('admin:assistant', 'admin'))
        ;


        $this->add(new Zend_Acl_Resource('testmodule'))
             ->add(new Zend_Acl_Resource('testmodule:index', 'testmodule'));

        $this->add(new Zend_Acl_Resource('user'))
                ->add(new Zend_Acl_Resource('user:index', 'user'))
                ->add(new Zend_Acl_Resource('user:timeslot', 'user'));


        $this->add(new Zend_Acl_Resource('default'))
                ->add(new Zend_Acl_Resource('default:index', 'default'))
                ->add(new Zend_Acl_Resource('default:profile', 'profile'))
                ->add(new Zend_Acl_Resource('default:search', 'default'))
                ->add(new Zend_Acl_Resource('default:appointment', 'appointment'))
                ->add(new Zend_Acl_Resource('default:sitemap', 'sitemap'))
                ->add(new Zend_Acl_Resource('default:xmlsitemap', 'xmlsitemap'))

        ;
        /** Default module */
    }

    public function setPrivilages() {
        /* guest */
        $this->allow('guest', array('default:index', 'default:profile', 'default:search', 'default:appointment', 'admin:login', 'testmodule:index', 'default:sitemap', 'default:xmlsitemap'));
        $this->allow('doctor', array('user:index','user:timeslot'));
        $this->allow('assistant', array('user:index','user:timeslot'));

        $this->allow('patient', array('user:index'), array('index','patient-dashboard','appointment-detail','patient-edit','cancel'));
        $this->deny('doctor', array('user:index'), array('patient-dashboard','appointment-detail','patient-edit'));// doctor should not goto the patient's urls
        $this->deny('assistant', array('user:index'), array('patient-dashboard','appointment-detail','patient-edit'));// assistant should not goto the patient's urls

        /* user */
        //$this->allow('user', array('default:gapper'));
        //$this->deny();
        /* administrator */
        $this->allow('administrator');
        $this->allow('subadmin');
        $this->deny('administrator', array('user:index','user:timeslot'));

        
        $array_act = array('admin:doctor', 'admin:appointment', 'admin:patient', 'admin:article',
                            'admin:phoneappointment', 'admin:hospital-affiliation', 'admin:award',
                            'admin:timeslot', 'admin:patient-comment', 'admin:category', 'admin:reason-for-visit',
                            'admin:association', 'admin:insurance-company', 'admin:userlevel', 'admin:user',
                            'admin:email-template'
                            );
        $this->deny('subadmin', $array_act, array('delete'));
        $this->allow('subadmin', 'admin:user', array('change-password'));
       
    }

}

?>