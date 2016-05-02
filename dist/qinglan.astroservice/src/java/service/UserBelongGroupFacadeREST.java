/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import entity.UserBelongGroup;
import java.util.List;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;
import javax.ws.rs.*;

/**
 *
 * @author roxy
 */
@Stateless
@Path("entity.userbelonggroup")
public class UserBelongGroupFacadeREST extends AbstractFacade<UserBelongGroup> {
    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;

    public UserBelongGroupFacadeREST() {
        super(UserBelongGroup.class);
    }

    @POST
    @Override
    @Consumes({"application/xml", "application/json"})
    public void create(UserBelongGroup entity) {
        super.create(entity);
    }

    @PUT
    @Override
    @Consumes({"application/xml", "application/json"})
    public void edit(UserBelongGroup entity) {
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
    public UserBelongGroup find(@PathParam("id") Long id) {
        return super.find(id);
    }

    @GET
    @Override
    @Produces({"application/xml", "application/json"})
    public List<UserBelongGroup> findAll() {
        return super.findAll();
    }

    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public List<UserBelongGroup> findRange(@PathParam("from") Integer from, @PathParam("to") Integer to) {
        return super.findRange(new int[]{from, to});
    }

    @GET
    @Path("count")
    @Produces("text/plain")
    public String countREST() {
        return String.valueOf(super.count());
    }

    @java.lang.Override
    protected EntityManager getEntityManager() {
        return em;
    }
    
}
