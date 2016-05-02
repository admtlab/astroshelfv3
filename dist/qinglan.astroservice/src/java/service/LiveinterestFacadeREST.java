/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import admt.message.ConfluenceCommunications;
import admt.message.DataMessage;
import entity.Liveinterest;
import entity.Notification;
import java.io.*;
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;
import javax.persistence.TypedQuery;
import javax.ws.rs.*;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;
import javax.xml.stream.XMLStreamWriter;
import org.codehaus.jackson.JsonGenerationException;
import org.codehaus.jackson.map.JsonMappingException;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jettison.mapped.Configuration;
import org.codehaus.jettison.mapped.MappedNamespaceConvention;
import org.codehaus.jettison.mapped.MappedXMLStreamWriter;
import com.sun.grizzly.websockets.WebSocket;
import com.sun.grizzly.websockets.WebSocketAdapter;
import com.sun.grizzly.websockets.WebSocketClient;
import javax.ws.rs.core.Response;

/**
 *
 * @author roxy
 */
@Stateless
@Path("liveinterest")
public class LiveinterestFacadeREST extends AbstractFacade<Liveinterest> {

    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;

    public LiveinterestFacadeREST() {
        super(Liveinterest.class);
    }

    @POST
    @Path("add")
    @Consumes({/*"application/xml",*/ "application/json"})
    @Produces({/*"application/xml",*/ "application/json"})
    public Response addLiveInterest(Liveinterest entity) {
        super.create(entity);
        getEntityManager().flush();
        System.out.println("from adding live interest to confluence");
        
        ConfluenceCommunications cm = new ConfluenceCommunications();
        cm.sendNewLiveinterest(entity);
        
        return Response.status(Response.Status.OK).entity(entity).header("Access-Control-Allow-Origin", "*").build();
    }

    @PUT
    @Override
    @Consumes({"application/xml", "application/json"})
    public void edit(Liveinterest entity) {
        super.edit(entity);
    }

    @DELETE
    @Path("{id}")
    public void remove(@PathParam("id") Long id) {
        super.remove(super.find(id));
    }

    @GET
    @Path("{id}")
    @Produces({"application/xml", "application/json"})
    public Liveinterest find(@PathParam("id") Long id) {
        return super.find(id);
    }

    @POST
    @Path("{id}/deactivate")
    @Produces({"application/xml", "application/json"})
    public Response deactivate(@PathParam("id") Long id) {
        Liveinterest li =  super.find(id);
        li.setActive((short)0);
        
        ConfluenceCommunications cm = new ConfluenceCommunications();
        cm.sendNewLiveinterest(li);
        
        return Response.status(Response.Status.OK).entity(li).header("Access-Control-Allow-Origin", "*").build();
    }
    
    @GET
    @Override
    @Produces({"application/xml", "application/json"})
    public List<Liveinterest> findAll() {
        return super.findAll();
    }

    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public List<Liveinterest> findRange(@PathParam("from") Integer from, @PathParam("to") Integer to) {
        return super.findRange(new int[]{from, to});
    }

    @GET
    @Path("count")
    @Produces("text/plain")
    public String countREST() {
        return String.valueOf(super.count());
    }

    @GET
    @Path("search")
    @Produces({"application/json", "application/xml"})
    public Response search(
            @DefaultValue("0")
            @QueryParam("active_user") Long active_user,
            @DefaultValue("")
            @QueryParam("keyword") String keyword,
            @DefaultValue("")
            @QueryParam("label") String label,
            @DefaultValue("null")
            @QueryParam("is_active") Boolean isActive,
            @DefaultValue("")
            @QueryParam("first_name") String first_name,
            @DefaultValue("")
            @QueryParam("username") String username,
            @DefaultValue("")
            @QueryParam("last_name") String last_name,
            @DefaultValue("0")
            @QueryParam("user_id") Long user_id) {
        
        String[][] attr = {{"p.keyword", keyword}, {"p.label", label},
            {"p.userId.fname", first_name}, {"p.userId.username", username}, {"p.userId.lname", last_name}};


        String q = "SELECT p from Liveinterest p";
        Boolean isFirst = true;

        for (int i = 0; i < attr.length; i++) {
            if (!attr[i][1].equals("")) {
                if (isFirst) {
                    q = q + " WHERE ";
                    isFirst = false;
                } else {
                    q = q + " AND ";
                }

                q = q + attr[i][0] + " LIKE \"%" + attr[i][1] + "%\"";
            }
        }

        if (user_id != 0) {
            if (isFirst) {
                q = q + " WHERE ";
                isFirst = false;
            } else {
                q = q + " AND ";
            }

            q = q + " p.userId =" + user_id;
        }

        if (isActive != null) {
            if (isFirst) {
                q = q + " WHERE ";
                isFirst = false;
            } else {
                q = q + " AND ";
            }

            q = q + " p.active =" + isActive;
        }


        TypedQuery<Liveinterest> query = getEntityManager().createQuery(q, Liveinterest.class);

        return Response.status(Response.Status.OK).entity(query.getResultList()).header("Access-Control-Allow-Origin", "*").build();
    }

    @java.lang.Override
    protected EntityManager getEntityManager() {
        return em;
    }

    @GET
    @Path("{liveinterestId}/notifications")
    @Produces({"application/xml", "application/json"})
    public List<Notification> getNotifications(
            @PathParam("liveinterestId") Long liId,
            @DefaultValue("0")
            @QueryParam("actingUserId") Long actingUserId,
            @QueryParam("markedRead") Boolean markedRead) {


        if (super.find(liId).getUserId().getUserId() == actingUserId) {
            if (markedRead == null) {
                return new ArrayList<Notification>(super.find(liId).getNotificationCollection());
            }

            TypedQuery<Notification> q = getEntityManager().createNamedQuery("Liveinterest.findNotificationsByInterestId", Notification.class);
            q.setParameter("interestId", liId);

            if (markedRead) {
                q.setParameter("isRead", 1);

            } else {
                q.setParameter("isRead", 0);
            }
            return q.getResultList();
        } else {
            return new ArrayList<Notification>();
        }
    }
}
