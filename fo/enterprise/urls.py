#!/usr/bin/env python
#-*- coding: utf-8 -*-
#Copyright (C) 2011 ISVTEC SARL
#$Id$
__author__ = "Ousmane Wilane ♟ <ousmane@wilane.org>"
__date__   = "Fri Nov 11 11:58:14 2011"

from django.conf.urls.defaults import patterns, url

urlpatterns = patterns('',
                       url(r'^add$', 'enterprise.views.add_company', name='add_company'),
                       url(r'^change/(?P<customer_id>\d+)$', 'enterprise.views.change_company', name='change_company'),
                       url(r'^invite$', 'enterprise.views.invite_user', name='invite_user'),
                       url(r'^revocations$', 'enterprise.views.revoke_invitations', name='revoke_invitations'),
                       url(r'^accept/(?P<token>\w+)$', 'enterprise.views.accept_invitation', name='accept_invitation'),
                       url(r'^revoke/(?P<token>\w+)$', 'enterprise.views.revoke_invitation', name='revoke_invitation'),
                       url(r'^resend/invitation/(?P<token>\w+)$', 'enterprise.views.resend_invitation', name='resend_invitation'),
)
