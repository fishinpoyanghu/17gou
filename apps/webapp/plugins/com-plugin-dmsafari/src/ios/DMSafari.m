/********* DMSafari.m Cordova Plugin Implementation *******/

#import <Cordova/CDV.h>

@interface DMSafari : CDVPlugin {
  // Member variables go here.
}

@property (nonatomic, strong) CDVInvokedUrlCommand *command;

- (void)transWeb:(CDVInvokedUrlCommand*)command;

@end

@implementation DMSafari

- (void)transWeb:(CDVInvokedUrlCommand*)command {
    CDVPluginResult* pluginResult = nil;
    NSString* url = [command.arguments objectAtIndex:0];

    if (url != nil && [url length] > 0) {
        self.command = command;
        [[UIApplication sharedApplication] openURL:[NSURL URLWithString:url]];
    } else {
        pluginResult = [CDVPluginResult resultWithStatus:CDVCommandStatus_ERROR];
        [self.commandDelegate sendPluginResult:pluginResult callbackId:command.callbackId];
        
    }
}

#pragma mark overwrite the openURL from ionic

- (void)handleOpenURL:(NSNotification *)notification {
    NSString *obj = [notification.object absoluteString];
    if([obj rangeOfString:@"dmsafari"].location != NSNotFound) {
        CDVPluginResult *pluginResult = [CDVPluginResult resultWithStatus:CDVCommandStatus_OK messageAsString:obj];
        [self.commandDelegate sendPluginResult:pluginResult callbackId:self.command.callbackId];
    }
}

@end
